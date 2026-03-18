<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BootstrapUserCommandTest extends TestCase
{
    public function test_it_creates_user_via_interactive_cli_command(): void
    {
        $this->artisan('platform:bootstrap-user')
            ->expectsQuestion('Full name', 'Platform Owner')
            ->expectsQuestion('Email address', 'owner@example.com')
            ->expectsQuestion('Password (min 8 chars)', 'super-secret-123')
            ->expectsQuestion('Confirm password', 'super-secret-123')
            ->expectsConfirmation('Mark email as verified?', true)
            ->assertExitCode(0);

        $user = User::query()->where('email', 'owner@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame(User::ROLE_ADMIN, $user->role);
        $this->assertTrue(Hash::check('super-secret-123', $user->password));
    }

    public function test_it_fails_validation_for_duplicate_email(): void
    {
        User::query()->create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'password' => Hash::make('password-123'),
        ]);

        $this->artisan('platform:bootstrap-user')
            ->expectsQuestion('Full name', 'Another User')
            ->expectsQuestion('Email address', 'existing@example.com')
            ->expectsQuestion('Password (min 8 chars)', 'new-password-123')
            ->expectsQuestion('Confirm password', 'new-password-123')
            ->expectsConfirmation('Mark email as verified?', true)
            ->assertExitCode(1);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_public_signup_endpoint_is_disabled(): void
    {
        $response = $this->postJson('/register', [
            'name' => 'Public User',
            'email' => 'public@example.com',
            'password' => 'password-123',
            'password_confirmation' => 'password-123',
        ]);

        $response
            ->assertStatus(403)
            ->assertJson([
                'message' => 'Public signup is disabled. Ask an administrator to provision your account.',
            ]);
    }
}




