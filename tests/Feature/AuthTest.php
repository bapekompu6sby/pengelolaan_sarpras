<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_bookings()
    {
        $response = $this->get(route('bookings'));
        
        // Ensure redirect to login
        $response->assertRedirect('/login');
    }

    public function test_user_cannot_access_admin_transactions()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
        $user->role = 'user';
        $user->save();

        $response = $this->actingAs($user)->get(route('transactions'));
        
        $response->assertStatus(403);
    }

    public function test_login_redirect_admin()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
        $user->role = 'admin';
        $user->save();

        $response = $this->post('/login', [
            'login' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/');
        
        $this->actingAs($user)->get('/')->assertRedirect(route('dashboardAdmin'));
    }

    public function test_login_redirect_user()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
        $user->role = 'user';
        $user->save();

        $response = $this->post('/login', [
            'login' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/');
        
        $this->actingAs($user)->get('/')->assertRedirect(route('dashboard'));
    }

    public function test_login_invalid_credentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }
}
