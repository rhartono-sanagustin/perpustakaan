<?php

namespace Tests\Feature\Security;

use App\Models\Author;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Loan;
use App\Models\LoanRequest;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class PolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_borrowers_cannot_manage_catalog_or_master_data(): void
    {
        $guru = User::factory()->guru()->create();
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create();
        $copy = BookCopy::factory()->create();
        $category = Category::factory()->create();
        $author = Author::factory()->create();
        $publisher = Publisher::factory()->create();

        foreach ([$guru, $siswa] as $borrower) {
            $this->assertFalse(Gate::forUser($borrower)->allows('viewAny', Book::class));
            $this->assertFalse(Gate::forUser($borrower)->allows('update', $book));
            $this->assertFalse(Gate::forUser($borrower)->allows('archive', $book));
            $this->assertFalse(Gate::forUser($borrower)->allows('create', BookCopy::class));
            $this->assertFalse(Gate::forUser($borrower)->allows('update', $copy));
            $this->assertFalse(Gate::forUser($borrower)->allows('create', Category::class));
            $this->assertFalse(Gate::forUser($borrower)->allows('update', $category));
            $this->assertFalse(Gate::forUser($borrower)->allows('create', Author::class));
            $this->assertFalse(Gate::forUser($borrower)->allows('update', $author));
            $this->assertFalse(Gate::forUser($borrower)->allows('create', Publisher::class));
            $this->assertFalse(Gate::forUser($borrower)->allows('update', $publisher));
        }
    }

    public function test_admin_can_manage_catalog_and_master_data(): void
    {
        $admin = User::factory()->admin()->create();
        $book = Book::factory()->create();
        $copy = BookCopy::factory()->create();
        $category = Category::factory()->create();
        $author = Author::factory()->create();
        $publisher = Publisher::factory()->create();

        $this->assertTrue(Gate::forUser($admin)->allows('viewAny', Book::class));
        $this->assertTrue(Gate::forUser($admin)->allows('create', Book::class));
        $this->assertTrue(Gate::forUser($admin)->allows('update', $book));
        $this->assertTrue(Gate::forUser($admin)->allows('archive', $book));
        $this->assertTrue(Gate::forUser($admin)->allows('activate', $book));
        $this->assertTrue(Gate::forUser($admin)->allows('create', BookCopy::class));
        $this->assertTrue(Gate::forUser($admin)->allows('update', $copy));
        $this->assertTrue(Gate::forUser($admin)->allows('create', Category::class));
        $this->assertTrue(Gate::forUser($admin)->allows('update', $category));
        $this->assertTrue(Gate::forUser($admin)->allows('create', Author::class));
        $this->assertTrue(Gate::forUser($admin)->allows('update', $author));
        $this->assertTrue(Gate::forUser($admin)->allows('create', Publisher::class));
        $this->assertTrue(Gate::forUser($admin)->allows('update', $publisher));
    }

    public function test_borrower_can_only_view_own_loan(): void
    {
        $admin = User::factory()->admin()->create();
        $guru = User::factory()->guru()->create();
        $siswa = User::factory()->siswa()->create();
        $otherSiswa = User::factory()->siswa()->create();
        $loan = Loan::factory()->create(['user_id' => $siswa->id]);

        $this->assertTrue(Gate::forUser($siswa)->allows('view', $loan));
        $this->assertTrue(Gate::forUser($admin)->allows('view', $loan));
        $this->assertFalse(Gate::forUser($guru)->allows('view', $loan));
        $this->assertFalse(Gate::forUser($otherSiswa)->allows('view', $loan));

        $this->assertFalse(Gate::forUser($siswa)->allows('markReturned', $loan));
        $this->assertFalse(Gate::forUser($guru)->allows('markReturned', $loan));
        $this->assertTrue(Gate::forUser($admin)->allows('markReturned', $loan));
        $this->assertFalse(Gate::forUser($admin)->allows('delete', $loan));
    }

    public function test_returned_loan_cannot_be_marked_returned_again(): void
    {
        $admin = User::factory()->admin()->create();
        $loan = Loan::factory()->returned()->create(['user_id' => User::factory()->siswa()->create()->id]);

        $this->assertFalse(Gate::forUser($admin)->allows('markReturned', $loan));
    }

    public function test_borrower_can_only_view_and_cancel_own_request(): void
    {
        $admin = User::factory()->admin()->create();
        $guru = User::factory()->guru()->create();
        $siswa = User::factory()->siswa()->create();
        $otherSiswa = User::factory()->siswa()->create();
        $loanRequest = LoanRequest::factory()->create(['user_id' => $siswa->id]);

        $this->assertTrue(Gate::forUser($siswa)->allows('view', $loanRequest));
        $this->assertTrue(Gate::forUser($admin)->allows('view', $loanRequest));
        $this->assertFalse(Gate::forUser($guru)->allows('view', $loanRequest));
        $this->assertFalse(Gate::forUser($otherSiswa)->allows('view', $loanRequest));

        $this->assertTrue(Gate::forUser($siswa)->allows('cancel', $loanRequest));
        $this->assertTrue(Gate::forUser($admin)->allows('cancel', $loanRequest));
        $this->assertFalse(Gate::forUser($guru)->allows('cancel', $loanRequest));
        $this->assertFalse(Gate::forUser($otherSiswa)->allows('cancel', $loanRequest));

        $this->assertTrue(Gate::forUser($admin)->allows('approve', $loanRequest));
        $this->assertTrue(Gate::forUser($admin)->allows('reject', $loanRequest));
        $this->assertFalse(Gate::forUser($siswa)->allows('approve', $loanRequest));
        $this->assertFalse(Gate::forUser($guru)->allows('reject', $loanRequest));
    }

    public function test_request_flow_rules_are_enforced_by_policy(): void
    {
        $admin = User::factory()->admin()->create();
        $siswa = User::factory()->siswa()->create();

        $rejected = LoanRequest::factory()->rejected()->create(['user_id' => $siswa->id]);
        $this->assertFalse(Gate::forUser($siswa)->allows('cancel', $rejected));
        $this->assertFalse(Gate::forUser($admin)->allows('approve', $rejected));
        $this->assertFalse(Gate::forUser($admin)->allows('checkout', $rejected));

        $approved = LoanRequest::factory()->approved()->create(['user_id' => $siswa->id]);
        $this->assertTrue(Gate::forUser($siswa)->allows('cancel', $approved));
        $this->assertTrue(Gate::forUser($admin)->allows('checkout', $approved));
        $this->assertFalse(Gate::forUser($siswa)->allows('checkout', $approved));

        $expired = LoanRequest::factory()->approved()->create([
            'user_id' => $siswa->id,
            'expires_at' => now()->subDay(),
        ]);
        $this->assertFalse(Gate::forUser($admin)->allows('checkout', $expired));
    }

    public function test_user_policy_protects_profiles_and_self_status_changes(): void
    {
        $admin = User::factory()->admin()->create();
        $siswa = User::factory()->siswa()->create();
        $otherSiswa = User::factory()->siswa()->create();

        $this->assertTrue(Gate::forUser($siswa)->allows('view', $siswa));
        $this->assertFalse(Gate::forUser($siswa)->allows('view', $otherSiswa));
        $this->assertTrue(Gate::forUser($admin)->allows('view', $otherSiswa));
        $this->assertTrue(Gate::forUser($admin)->allows('update', $otherSiswa));
        $this->assertFalse(Gate::forUser($admin)->allows('update', $admin));

        $this->assertTrue(Gate::forUser($siswa)->allows('updateProfile', $siswa));
        $this->assertFalse(Gate::forUser($siswa)->allows('updateProfile', $otherSiswa));

        $this->assertFalse(Gate::forUser($admin)->allows('delete', $admin));
        $this->assertFalse(Gate::forUser($admin)->allows('resetPassword', $admin));
        $this->assertFalse(Gate::forUser($admin)->allows('toggleStatus', $admin));
        $this->assertTrue(Gate::forUser($admin)->allows('toggleStatus', $otherSiswa));
        $this->assertFalse(Gate::forUser($siswa)->allows('toggleStatus', $otherSiswa));
    }

    public function test_borrower_cannot_open_master_data_endpoints(): void
    {
        $siswa = User::factory()->siswa()->create();

        $this->actingAs($siswa)->get(route('admin.books.index'))->assertForbidden();
        $this->actingAs($siswa)->get(route('admin.copies.index'))->assertForbidden();
        $this->actingAs($siswa)->get(route('admin.categories.index'))->assertForbidden();
        $this->actingAs($siswa)->get(route('admin.authors.index'))->assertForbidden();
        $this->actingAs($siswa)->get(route('admin.publishers.index'))->assertForbidden();
    }
}
