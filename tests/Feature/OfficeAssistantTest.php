<?php

namespace Tests\Feature;

use App\Ai\Agents\OfficeAssistant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Models\Conversation;
use Laravel\Ai\Models\ConversationMessage;
use Tests\TestCase;

class OfficeAssistantTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('ai.assistant.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_chat_page(): void
    {
        $this->actingUser();

        $this->get(route('ai.assistant.index'))
            ->assertOk()
            ->assertSee('AI Assistant');
    }

    public function test_send_returns_error_when_gemini_key_is_missing(): void
    {
        config()->set('ai.providers.gemini.key', '');

        $this->actingUser();

        $this->postJson(route('ai.assistant.send'), ['message' => 'Halo'])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Asisten AI belum aktif karena API key Gemini belum dikonfigurasi. Hubungi administrator.');
    }

    public function test_message_requires_text(): void
    {
        config()->set('ai.providers.gemini.key', 'test-key');
        OfficeAssistant::fake(['Balasan test']);

        $this->actingUser();

        $this->postJson(route('ai.assistant.send'), ['message' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('message');
    }

    public function test_send_creates_conversation_and_returns_reply(): void
    {
        config()->set('ai.providers.gemini.key', 'test-key');
        OfficeAssistant::fake(['Balasan test']);

        $this->actingUser();

        $response = $this->postJson(route('ai.assistant.send'), ['message' => 'Apa itu 3DY Group?'])
            ->assertOk()
            ->assertJsonPath('message', 'Balasan test');

        $conversationId = $response->json('conversation_id');
        $this->assertNotNull($conversationId);

        $this->assertDatabaseHas('agent_conversations', ['id' => $conversationId]);
        $this->assertSame(2, ConversationMessage::where('conversation_id', $conversationId)->count());
    }

    public function test_send_continues_existing_conversation(): void
    {
        config()->set('ai.providers.gemini.key', 'test-key');
        OfficeAssistant::fake(['Balasan pertama', 'Balasan kedua']);

        $this->actingUser();

        $first = $this->postJson(route('ai.assistant.send'), ['message' => 'Pertanyaan pertama'])
            ->assertOk()
            ->json('conversation_id');

        $second = $this->postJson(route('ai.assistant.send'), [
            'message' => 'Pertanyaan kedua',
            'conversation_id' => $first,
        ])->assertOk();

        $this->assertSame($first, $second->json('conversation_id'));
        $this->assertSame(4, ConversationMessage::where('conversation_id', $first)->count());
    }

    public function test_send_rejects_conversation_owned_by_other_user(): void
    {
        config()->set('ai.providers.gemini.key', 'test-key');
        OfficeAssistant::fake(['Balasan test']);

        $owner = $this->actingAs(User::factory()->create());
        $conversationId = $this->postJson(route('ai.assistant.send'), ['message' => 'Milik user A'])
            ->json('conversation_id');

        $this->actingAs(User::factory()->create())
            ->postJson(route('ai.assistant.send'), [
                'message' => 'Coba akses',
                'conversation_id' => $conversationId,
            ])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Percakapan tidak valid. Silakan mulai percakapan baru.');

        $this->assertSame(1, Conversation::where('id', $conversationId)->count());
    }
}
