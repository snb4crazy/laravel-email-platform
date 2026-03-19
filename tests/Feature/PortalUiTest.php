<?php

namespace Tests\Feature;

use App\Models\Site;
use App\Models\User;
use Tests\TestCase;

class PortalUiTest extends TestCase
{
    // ── Dashboard redirect ─────────────────────────────────────────────────

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/portal')->assertRedirect('/login');
        $this->get('/sites')->assertRedirect('/login');
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_regular_user_login_redirects_to_portal_dashboard(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('portal.dashboard'));
    }

    public function test_portal_dashboard_loads_for_user(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $this->actingAs($user)
            ->get('/portal')
            ->assertOk()
            ->assertViewIs('portal.dashboard');
    }

    // ── Site management ────────────────────────────────────────────────────

    public function test_user_can_view_their_sites(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $this->actingAs($user)->get('/sites')->assertOk();
    }

    public function test_user_can_access_site_create_form(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $this->actingAs($user)->get('/sites/create')->assertOk();
    }

    public function test_user_can_create_a_site(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $this->actingAs($user)
            ->post('/sites', [
                'name' => 'My Test Site',
                'domain' => 'test.example.com',
                'auth_mode' => 'none',
                'is_active' => '1',
            ])
            ->assertRedirect('/sites');

        $this->assertDatabaseHas('sites', [
            'name' => 'My Test Site',
            'domain' => 'test.example.com',
            'tenant_id' => $user->id,
        ]);
    }

    public function test_user_can_view_their_own_site(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $site = Site::factory()->for($user, 'tenant')->create();

        $this->actingAs($user)
            ->get("/sites/{$site->id}")
            ->assertOk()
            ->assertViewIs('portal.sites.show');
    }

    public function test_user_cannot_view_another_users_site(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_USER]);
        $other = User::factory()->create(['role' => User::ROLE_USER]);
        $site = Site::factory()->for($owner, 'tenant')->create();

        $this->actingAs($other)
            ->get("/sites/{$site->id}")
            ->assertForbidden();
    }

    public function test_user_can_update_their_site(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $site = Site::factory()->for($user, 'tenant')->create();

        $this->actingAs($user)
            ->put("/sites/{$site->id}", [
                'name' => 'Updated Name',
                'domain' => $site->domain,
                'auth_mode' => 'none',
                'is_active' => '1',
            ])
            ->assertRedirect("/sites/{$site->id}");

        $this->assertDatabaseHas('sites', ['id' => $site->id, 'name' => 'Updated Name']);
    }

    public function test_user_cannot_update_another_users_site(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_USER]);
        $other = User::factory()->create(['role' => User::ROLE_USER]);
        $site = Site::factory()->for($owner, 'tenant')->create();

        $this->actingAs($other)
            ->put("/sites/{$site->id}", [
                'name' => 'Hacked',
                'domain' => $site->domain,
                'auth_mode' => 'none',
            ])
            ->assertForbidden();
    }

    public function test_user_can_delete_their_site(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $site = Site::factory()->for($user, 'tenant')->create();

        $this->actingAs($user)
            ->delete("/sites/{$site->id}")
            ->assertRedirect('/sites');

        $this->assertSoftDeleted('sites', ['id' => $site->id]);
    }

    // ── Messages ───────────────────────────────────────────────────────────

    public function test_user_can_view_messages_page(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $this->actingAs($user)->get('/messages')->assertOk();
    }

    // ── Templates ──────────────────────────────────────────────────────────

    public function test_user_can_view_templates_page(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $this->actingAs($user)->get('/templates')->assertOk();
    }

    // ── Admin portal views ─────────────────────────────────────────────────

    public function test_admin_can_view_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertViewIs('admin.dashboard');
    }

    public function test_admin_can_view_all_sites(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin/sites')
            ->assertOk()
            ->assertViewIs('admin.sites.index');
    }

    public function test_admin_can_view_all_messages(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin/messages')
            ->assertOk()
            ->assertViewIs('admin.messages.index');
    }

    public function test_regular_user_cannot_access_admin_area(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $this->actingAs($user)->get('/admin')->assertForbidden();
        $this->actingAs($user)->get('/admin/sites')->assertForbidden();
        $this->actingAs($user)->get('/admin/messages')->assertForbidden();
    }
}
