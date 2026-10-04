<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_google_account_is_authenticated_without_a_new_user(): void
    {
        $user = User::factory()->create([
            'email' => 'owner@example.com',
            'role' => 'designer',
        ]);

        $this->mockGoogleUser('owner@example.com', 'Owner');

        $this->get(route('google.callback'))
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertSame(1, User::query()->count());
        $this->assertSame('designer', $user->fresh()->role);
    }

    public function test_unknown_google_account_is_not_created(): void
    {
        $this->mockGoogleUser('new-person@example.com', 'New Person');

        $this->get(route('google.callback'))
            ->assertRedirect(route('register'))
            ->assertSessionHas('status', 'No RenovaHub account was found for this Google account. Please register first.');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', [
            'email' => 'new-person@example.com',
        ]);
    }

    private function mockGoogleUser(string $email, string $name): void
    {
        $googleUser = (new SocialiteUser)->map([
            'id' => 'google-'.$email,
            'email' => $email,
            'name' => $name,
        ]);

        $provider = Mockery::mock(\Laravel\Socialite\Contracts\Provider::class);
        $provider->shouldReceive('user')->once()->andReturn($googleUser);

        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);
    }
}
