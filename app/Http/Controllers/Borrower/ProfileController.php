<?php

namespace App\Http\Controllers\Borrower;

use App\Http\Controllers\Controller;
use App\Http\Requests\Borrower\UpdateProfileRequest;
use App\Services\CirculationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(private readonly CirculationService $circulation) {}

    public function edit(Request $request): View
    {
        $user = $request->user();
        $this->authorize('updateProfile', $user);

        return view('borrower.profile', [
            'user' => $user,
            'quota' => $this->circulation->quotaSummary($user),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $this->authorize('updateProfile', $user);

        $data = $request->validated();
        unset($data['current_password']);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('borrower.profile.edit')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}
