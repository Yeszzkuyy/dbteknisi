<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_login_kicks_previous_sessions_and_clears_remember_token(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['remember_token' => Str::random(60)])->save();

        DB::table('sessions')->insert([
            'id' => Str::random(40),
            'user_id' => $user->id,
            'ip_address' => '10.0.0.1',
            'user_agent' => 'laptop-a',
            'payload' => base64_encode(json_encode([])),
            'last_activity' => now()->timestamp,
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $kicked = DB::table('sessions')->where('user_agent', 'laptop-a')->first();
        $this->assertNotNull($kicked);
        $this->assertNull($kicked->user_id);
        $this->assertArrayHasKey('kicked_at', json_decode(base64_decode($kicked->payload), true));
        $this->assertNull($user->fresh()->remember_token);
    }

    public function test_login_screen_shows_kick_notice_for_terminated_session(): void
    {
        $this->withSession(['kicked_at' => now()->toDateTimeString()])
            ->get('/login')
            ->assertStatus(200)
            ->assertSee('login di perangkat lain');
    }
}
