<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateHomeownerProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $user = request()->user();
        abort_unless($user->isHomeowner(), 403);

        $projects = $user->projects()->get();

        return view('homeowner.profile.edit', [
            'user' => $user,
            'active' => $projects->where('status', 'in_progress')->count(),
            'completed' => $projects->where('status', 'completed')->count(),
        ]);
    }

    public function update(UpdateHomeownerProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->update($request->safe()->only(['name', 'phone', 'address']));

        if ($request->hasFile('photo')) {
            $user->updateProfilePhoto($request->file('photo'));
        }

        return back()->with('status', 'Profile updated.');
    }
}
