<?php

namespace Tests\Feature;

use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StaffLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/staff/login');

        $response->assertStatus(200);
    }

    public function test_staff_can_login_with_correct_credentials(): void
    {
        $staff = Staff::create([
            'fullname' => 'Test Staff',
            'password' => Hash::make('password123'),
            'role' => 'staff',
        ]);

        $response = $this->post('/staff/login', [
            'fullname' => 'Test Staff',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('staff.dashboard'));
        $this->assertAuthenticatedAs($staff, 'web');
    }

    public function test_staff_cannot_login_with_wrong_password(): void
    {
        Staff::create([
            'fullname' => 'Test Staff',
            'password' => Hash::make('password123'),
            'role' => 'staff',
        ]);

        $response = $this->post('/staff/login', [
            'fullname' => 'Test Staff',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest('web');
    }
}