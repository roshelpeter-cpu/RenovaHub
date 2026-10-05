<?php

namespace App\Http\Controllers\Designer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Designer\UpdateDesignerProfileRequest;
use App\Models\ProfessionalProject;
use App\Models\ProjectFeedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $profile = $request->user()->professionalProfile()->firstOrCreate(
            ['user_id' => $request->user()->id],
            ['professional_type' => 'designer', 'title' => 'Interior Designer'],
        );

        $profile->load(['caseStudies.images', 'reviews']);

        $feedback = ProjectFeedback::query()
            ->where('professional_id', $request->user()->id)
            ->where('role', 'designer')
            ->with('project')
            ->latest()
            ->get();

        return view('designer.profile.edit', compact('profile', 'feedback'));
    }

    public function update(UpdateDesignerProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->update(['name' => $request->validated('name')]);

        $profile = $user->professionalProfile()->firstOrCreate(
            ['user_id' => $user->id],
            ['professional_type' => 'designer'],
        );

        $data = $request->safe()->only(['title', 'location', 'bio', 'about', 'specialization', 'years_experience']);

        if ($request->hasFile('photo')) {
            $data['avatar_path'] = 'storage/'.$request->file('photo')->store('profiles/'.$user->id, 'public');
        }

        $profile->update($data);

        return back()->with('status', 'Profile updated.');
    }

    public function storePortfolio(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isDesigner(), 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'project_type' => ['required', 'string', 'max:120'],
            'location' => ['required', 'string', 'max:120'],
            'summary' => ['required', 'string', 'max:2000'],
            'images' => ['required', 'array', 'min:1', 'max:8'],
            'images.*' => ['image', 'max:5120'],
        ]);

        $profile = $request->user()->professionalProfile()->firstOrFail();
        $hero = 'storage/'.$request->file('images')[0]->store('portfolio/'.$profile->id, 'public');

        $case = $profile->caseStudies()->create([
            'slug' => Str::slug($data['title']).'-'.Str::lower(Str::random(4)),
            'title' => $data['title'],
            'project_type' => $data['project_type'],
            'category' => $data['project_type'],
            'location' => $data['location'],
            'summary' => $data['summary'],
            'overview' => $data['summary'],
            'hero_image' => $hero,
        ]);

        foreach ($request->file('images') as $index => $image) {
            $case->images()->create([
                'path' => $index === 0 ? $hero : 'storage/'.$image->store('portfolio/'.$profile->id, 'public'),
                'caption' => $data['title'],
                'image_type' => 'gallery',
                'sort_order' => $index,
            ]);
        }

        return back()->with('status', 'Portfolio project added.');
    }

    public function project(Request $request, ProfessionalProject $caseStudy): View
    {
        abort_unless($caseStudy->professional_profile_id === $request->user()->professionalProfile?->id, 404);
        $caseStudy->load('images');

        return view('designer.profile.project', ['project' => $caseStudy]);
    }
}
