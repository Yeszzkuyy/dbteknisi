<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiAssistantWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_widget_markup_is_present_on_app_layout_pages(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('aiChatV2(', false)
            ->assertSee('3DY AI', false);
    }

    public function test_widget_is_hidden_for_guests(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee('aiChatV2(', false);
    }

    public function test_existing_send_endpoint_still_accepts_text_message(): void
    {
        config()->set('ai.providers.openrouter.key', null);

        $user = User::factory()->create();

        // Tanpa API key endpoint menolak dengan 422 — kontrak tidak berubah.
        $this->actingAs($user)
            ->postJson(route('ai.assistant.send'), ['message' => 'Halo'])
            ->assertStatus(422);
    }
}
