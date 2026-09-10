<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('settings.edit'))
            ->assertOk()
            ->assertSee('Setting');
    }

    public function test_user_can_update_preferences(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('settings.update'), [
                'theme' => 'dark',
                'locale' => 'en',
                'notify_email' => '1',
                'notify_system' => '0',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('settings.edit'));

        $user->refresh();

        $this->assertSame('dark', $user->preference('theme'));
        $this->assertSame('en', $user->preference('locale'));
        $this->assertTrue($user->preference('notify_email'));
        $this->assertFalse($user->preference('notify_system'));
    }

    public function test_preferences_are_validated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('settings.update'), [
                'theme' => 'neon',
                'locale' => 'fr',
            ])
            ->assertSessionHasErrors(['theme', 'locale']);
    }

    public function test_advanced_settings_requires_password_confirmation(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('settings.advanced'))
            ->assertRedirect(route('password.confirm'));
    }

    public function test_advanced_settings_is_accessible_after_password_confirmation(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['auth.password_confirmed_at' => now()->getTimestamp()])
            ->get(route('settings.advanced'))
            ->assertOk()
            ->assertSee('Pengaturan Lanjutan');
    }

    public function test_locale_preference_switches_app_language(): void
    {
        $user = User::factory()->create();
        $user->preferences = ['locale' => 'en'];
        $user->save();

        $this->actingAs($user)
            ->get(route('settings.edit'));

        $this->assertSame('en', App::getLocale());

        $user->preferences = ['locale' => 'id'];
        $user->save();

        $this->actingAs($user)
            ->get(route('settings.edit'));

        $this->assertSame('id', App::getLocale());
    }
}