<?php

namespace Tests\Feature;

use App\Ai\Agents\OfficeAssistant;
use App\Ai\Tools\GetLatestProjects;
use App\Ai\Tools\GetLeadDetails;
use App\Ai\Tools\GetMyTasks;
use App\Ai\Tools\GetProjectDetails;
use App\Ai\Tools\GetProjectProgress;
use App\Ai\Tools\GetSalesSummary;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Providers\Tools\FileSearch;
use Tests\TestCase;

class OfficeAssistantToolsTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_exposes_business_tools_for_authenticated_participant(): void
    {
        config()->set('ai.knowledge_base.store_id', 'store-test');
        $user = User::factory()->create();

        $agent = (new OfficeAssistant)->forUser($user);

        $tools = collect($agent->tools());
        $classes = $tools->map(fn ($tool) => $tool::class)->values();

        $this->assertContains(GetMyTasks::class, $classes);
        $this->assertContains(GetLatestProjects::class, $classes);
        $this->assertContains(GetProjectDetails::class, $classes);
        $this->assertContains(GetProjectProgress::class, $classes);
        $this->assertContains(GetSalesSummary::class, $classes);
        $this->assertContains(GetLeadDetails::class, $classes);
        $this->assertContains(FileSearch::class, $classes);
    }

    public function test_agent_without_participant_exposes_no_business_tools(): void
    {
        config()->set('ai.knowledge_base.store_id', 'store-test');

        $agent = new OfficeAssistant;

        $classes = collect($agent->tools())->map(fn ($tool) => $tool::class)->values();

        $this->assertContains(FileSearch::class, $classes);
        $this->assertNotContains(GetMyTasks::class, $classes);
        $this->assertNotContains(GetSalesSummary::class, $classes);
        $this->assertNotContains(GetLeadDetails::class, $classes);
    }

    public function test_business_tools_receive_the_conversation_participant(): void
    {
        $user = User::factory()->create();

        $agent = (new OfficeAssistant)->forUser($user);

        $tool = collect($agent->tools())->first(fn ($tool) => $tool instanceof GetMyTasks);

        $this->assertInstanceOf(GetMyTasks::class, $tool);
        $this->assertSame($user->id, $tool->user->id);
    }
}
