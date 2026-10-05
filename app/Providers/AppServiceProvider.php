<?php

namespace App\Providers;

use App\Events\ProjectInvitationResponded;
use App\Events\QuotationDecided;
use App\Listeners\RecordWorkspaceActivity;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\ProfessionalProfile;
use App\Models\Project;
use App\Policies\ConversationMessagePolicy;
use App\Policies\ConversationPolicy;
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
        Gate::policy(Conversation::class, ConversationPolicy::class);
        Gate::policy(ConversationMessage::class, ConversationMessagePolicy::class);

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

            $view->with('navCounts', [
                'messages' => ConversationMessage::query()
                    ->whereHas('conversation.participants', fn ($query) => $query->where('users.id', $user->id))
                    ->where('sender_id', '!=', $user->id)
                    ->where(function ($query) use ($user) {
                        $query->whereDoesntHave('conversation.participantRows', fn ($inner) => $inner->where('user_id', $user->id)->whereNotNull('last_read_at'))
                            ->orWhereHas('conversation.participantRows', function ($inner) use ($user) {
                                $inner->where('user_id', $user->id)
                                    ->whereColumn('conversation_participants.last_read_at', '<', 'conversation_messages.created_at');
                            });
                    })
                    ->count(),
                'notifications' => $user->unreadNotifications()->count(),
            ]);
        });

        View::composer('components.designer-layout', function ($view) {
            $user = auth()->user();

            if ($user === null || ! $user->isDesigner()) {
                return;
            }

            $view->with('navCounts', [
                'notifications' => $user->unreadNotifications()->count(),
            ]);
        });

        View::composer('components.contractor-layout', function ($view) {
            $user = auth()->user();

            if ($user === null || ! $user->isContractor()) {
                return;
            }

            $view->with('navCounts', [
                'notifications' => $user->unreadNotifications()->count(),
            ]);
        });
    }
}
