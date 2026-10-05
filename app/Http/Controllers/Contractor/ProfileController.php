<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contractor\UpdateProfileRequest;
use App\Models\ProfessionalProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $profile = $request->user()->professionalProfile;
        $profile?->load(['caseStudies.images', 'reviews']);

        return view('contractor.profile.edit', [
            'profile' => $profile,
            'user' => $request->user(),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->update(['name' => $request->string('name')->toString()]);

        $profile = $user->professionalProfile()->firstOrCreate(
            [],
            ['professional_type' => 'contractor', 'listed' => true],
        );

        $avatar = $profile->avatar_path;

        if ($request->hasFile('avatar')) {
            $avatar = 'storage/'.$request->file('avatar')->store('profiles', 'public');
        }

        $profile->update([
            'professional_type' => 'contractor',
            'business_name' => $request->input('business_name'),
            'title' => $request->input('title'),
            'location' => $request->input('location'),
            'about' => $request->input('about'),
            'bio' => $request->input('about'),
            'years_experience' => $request->input('years_experience'),
            'specialization' => $request->input('specialization'),
            'avatar_path' => $avatar,
        ]);

        return back()->with('status', 'Profile updated.');
    }

    public function storePortfolio(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'summary' => ['nullable', 'string', 'max:2000'],
            'location' => ['nullable', 'string', 'max:150'],
            'images' => ['required', 'array', 'min:1', 'max:8'],
            'images.*' => ['image', 'max:5120'],
        ]);

        $profile = $request->user()->professionalProfile()->firstOrCreate(
            [],
            ['professional_type' => 'contractor', 'listed' => true],
        );

        $case = $profile->caseStudies()->create([
            'title' => $data['title'],
            'slug' => Str::slug($data['title']).'-'.Str::lower(Str::random(4)),
            'summary' => $data['summary'] ?? null,
            'location' => $data['location'] ?? null,
            'hero_image' => 'storage/'.$request->file('images')[0]->store('portfolio', 'public'),
            'sort_order' => $profile->caseStudies()->count() + 1,
        ]);

        foreach ($request->file('images') as $index => $image) {
            $path = $index === 0 ? $case->hero_image : 'storage/'.$image->store('portfolio', 'public');
            $case->images()->create(['path' => $path, 'sort_order' => $index]);
        }

        return back()->with('status', 'Portfolio project added.');
    }

    public function project(Request $request, ProfessionalProject $caseStudy): View
    {
        abort_unless((int) $caseStudy->professional_profile_id === (int) $request->user()->professionalProfile?->id, 404);
        $caseStudy->load('images');

        return view('contractor.profile.project', ['caseStudy' => $caseStudy]);
    }
}
