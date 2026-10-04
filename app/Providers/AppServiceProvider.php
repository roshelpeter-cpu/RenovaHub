<?php

namespace App\Providers;

use App\Events\ProjectInvitationResponded;
use App\Events\QuotationDecided;
use App\Listeners\RecordWorkspaceActivity;
use App\Models\Message;
use App\Models\ProfessionalProfile;
use App\Models\Project;
use App\Policies\ProfessionalProfilePolicy;
use App\Policies\ProjectPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
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
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(ProfessionalProfile::class, ProfessionalProfilePolicy::class);

        Event::listen(ProjectInvitationResponded::class, [RecordWorkspaceActivity::class, 'handleInvitation']);
        Event::listen(QuotationDecided::class, [RecordWorkspaceActivity::class, 'handleQuotation']);

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Counts come from the database so the sidebar never shows a fixed badge.
        View::composer('components.homeowner-layout', function ($view) {
            $user = auth()->user();

            if ($user === null || ! $user->isHomeowner()) {
                return;
            }

            $projectIds = $user->projects()->select('id');

            $view->with('navCounts', [
                'messages' => Message::query()
                    ->whereIn('project_id', $projectIds)
                    ->whereNull('read_at')
                    ->where('sender_id', '!=', $user->id)
                    ->count(),
                'notifications' => $user->unreadNotifications()->count(),
            ]);
        });
    }
}
