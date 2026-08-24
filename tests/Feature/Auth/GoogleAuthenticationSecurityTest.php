<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use RuntimeException;
use Tests\TestCase;

class GoogleAuthenticationSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_google_email_cannot_be_linked_to_an_existing_account(): void
    {
        $user = User::factory()->create([
            'email' => 'owner@example.com',
            'google_id' => null,
        ]);

        $this->fakeGoogleUser('google-user-id', 'owner@example.com', false);

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertNull($user->fresh()->google_id);
    }

    public function test_verified_google_email_can_be_linked_to_an_existing_account(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'owner@example.com',
            'google_id' => null,
        ]);

        $this->fakeGoogleUser('google-user-id', 'owner@example.com', true);

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user->fresh());
        $this->assertSame('google-user-id', $user->fresh()->google_id);
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_verified_google_email_can_be_linked_with_uppercase_email_input(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'case-google@example.com',
            'google_id' => null,
        ]);

        $this->fakeGoogleUser('google-case-user-id', 'CASE-GOOGLE@EXAMPLE.COM', true);

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user->fresh());
        $this->assertSame('google-case-user-id', $user->fresh()->google_id);
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_google_login_failure_returns_to_login_screen(): void
    {
        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andThrow(new RuntimeException('Google is unavailable.'));

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    private function fakeGoogleUser(string $id, string $email, bool $emailVerified): void
    {
        $socialiteUser = (new SocialiteUser)->setRaw([
            'email_verified' => $emailVerified,
        ])->map([
            'id' => $id,
            'name' => 'Google User',
            'email' => $email,
        ]);

        $provider = new class($socialiteUser)
        {
            public function __construct(private SocialiteUser $user) {}

            public function user(): SocialiteUser
            {
                return $this->user;
            }
        };

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($provider);
    }
}
