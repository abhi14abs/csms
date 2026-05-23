<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class SecurityRoutesTest extends TestCase
{
    public function test_guest_cannot_submit_topup_application(): void
    {
        $response = $this->post(route('member.topup.submit'), [
            'new_amount' => 10000,
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_logout_requires_post_request(): void
    {
        $response = $this->get('/logout');

        $response->assertStatus(405);
    }

    public function test_authenticated_user_without_employee_profile_cannot_open_member_dashboard(): void
    {
        $user = new User([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'secret',
            'employee_number' => null,
            'is_admin' => false,
        ]);

        $response = $this->actingAs($user)->get(route('member.dashboard'));

        $response->assertForbidden();
    }
}
