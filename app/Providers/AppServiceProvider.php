<?php

namespace App\Providers;

use App\Models\Author;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Loan;
use App\Models\LoanRequest;
use App\Models\Publisher;
use App\Models\User;
use App\Policies\AuthorPolicy;
use App\Policies\BookCopyPolicy;
use App\Policies\BookPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\LoanPolicy;
use App\Policies\LoanRequestPolicy;
use App\Policies\PublisherPolicy;
use App\Policies\UserPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Carbon::setLocale(config('app.locale'));

        Gate::policy(Author::class, AuthorPolicy::class);
        Gate::policy(Book::class, BookPolicy::class);
        Gate::policy(BookCopy::class, BookCopyPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Loan::class, LoanPolicy::class);
        Gate::policy(LoanRequest::class, LoanRequestPolicy::class);
        Gate::policy(Publisher::class, PublisherPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }
}
