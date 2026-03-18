<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    public function test_guest_cannot_access_admin_user_pages(): void
    {
        $this->get('/admin/users')->assertRedirect('/login');
        $this->get('/admin/users/create')->assertRedirect('/login');
    }

    public function test_non_admin_cannot_access_admin_user_pages(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $this->actingAs($user)
            ->get('/admin/users')
            ->assertForbidden();
    }

    public function test_admin_can_create_new_user_from_admin_panel(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post('/admin/users', [
                'name' => 'New Team Member',
                'email' => 'new.user@example.com',
                'role' => User::ROLE_USER,
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
                'email_verified' => 1,
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'new.user@example.com',
            'role' => User::ROLE_USER,
        ]);
    }

    public function test_public_register_route_stays_disabled(): void
    {
        $this->postJson('/register', [
            'name' => 'Public User',
            'email' => 'public.user@example.com',
            'password' => 'public-password-123',
            'password_confirmation' => 'public-password-123',
        ])->assertStatus(403);
    }
}

