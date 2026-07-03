<?php

use App\Ai\Agents\TutorAgent;
use App\Enums\EstadoExamen;
use App\Models\AgentConversation;
use App\Models\AgentConversationMessage;
use App\Models\Examen;
use App\Models\Materia;
use App\Models\Topico;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can ask tutor and get response', function () {
    config(['ai.providers.openrouter.key' => 'testing-key']);
    TutorAgent::fake(['This is the tutor response.']);

    $response = actingAs($this->user)->postJson('/api/v1/tutor/ask', [
        'question' => 'How do I solve quadratic equations?',
    ]);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                'conversation_id',
                'response',
                'materia',
                'topico',
                'created_at',
            ],
        ])
        ->assertJsonPath('data.response', 'This is the tutor response.');

    $conversationId = $response->json('data.conversation_id');
    expect($conversationId)->not->toBeEmpty();
});

it('uses a local tutor fallback when OpenRouter key is missing', function () {
    config(['ai.providers.openrouter.key' => null]);

    $response = actingAs($this->user)->postJson('/api/v1/tutor/ask', [
        'question' => 'Dame una estrategia para lectura crítica',
    ]);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                'conversation_id',
                'response',
                'materia',
                'topico',
                'created_at',
            ],
        ]);

    expect($response->json('data.response'))->toContain('modo tutor local')
        ->and(AgentConversationMessage::where('user_id', $this->user->id)->count())->toBe(2);
});

it('can continue conversation with valid conversation id', function () {
    config(['ai.providers.openrouter.key' => 'testing-key']);

    $conversation = AgentConversation::create([
        'id' => (string) Str::uuid(),
        'user_id' => $this->user->id,
        'title' => 'Tutor chat',
    ]);
    AgentConversationMessage::create([
        'id' => (string) Str::uuid(),
        'conversation_id' => $conversation->id,
        'user_id' => $this->user->id,
        'agent' => TutorAgent::class,
        'role' => 'user',
        'content' => 'First question',
        'attachments' => '[]',
        'tool_calls' => '[]',
        'tool_results' => '[]',
        'usage' => '[]',
        'meta' => '[]',
    ]);

    TutorAgent::fake(['Follow-up answer.']);

    $response = actingAs($this->user)->postJson("/api/v1/tutor/continue/{$conversation->id}", [
        'question' => 'Tell me more.',
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('data.conversation_id', $conversation->id)
        ->assertJsonPath('data.response', 'Follow-up answer.');
});

it('cannot access tutor during active exam', function () {
    Examen::factory()->create([
        'user_id' => $this->user->id,
        'estado' => EstadoExamen::EnProgreso,
    ]);

    TutorAgent::fake(['Should not reach here.']);

    $response = actingAs($this->user)->postJson('/api/v1/tutor/ask', [
        'question' => 'What is 2+2?',
    ]);

    $response->assertForbidden();
    $response->assertJson(['message' => 'No puedes usar el tutor mientras tienes un examen activo.']);
});

it('returns materia and topico context when provided', function () {
    $materia = Materia::factory()->create(['nombre' => 'Matemáticas']);
    $topico = Topico::factory()->create([
        'materia_id' => $materia->id,
        'nombre' => 'Álgebra',
    ]);

    TutorAgent::fake(['Explanation about algebra.']);

    $response = actingAs($this->user)->postJson('/api/v1/tutor/ask', [
        'question' => 'Explain linear equations',
        'materia_id' => $materia->id,
        'topico_id' => $topico->id,
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('data.materia.id', $materia->id)
        ->assertJsonPath('data.materia.nombre', 'Matemáticas')
        ->assertJsonPath('data.topico.id', $topico->id)
        ->assertJsonPath('data.topico.nombre', 'Álgebra');
});

it('validates question is required', function () {
    TutorAgent::fake(['Response']);

    $response = actingAs($this->user)->postJson('/api/v1/tutor/ask', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('question');
});

it('validates invalid materia_id', function () {
    TutorAgent::fake(['Response']);

    $response = actingAs($this->user)->postJson('/api/v1/tutor/ask', [
        'question' => 'Help me',
        'materia_id' => 99999,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('materia_id');
});

it('validates topico belongs to materia when both provided', function () {
    $materia1 = Materia::factory()->create();
    $materia2 = Materia::factory()->create();
    $topicoOfMateria2 = Topico::factory()->create(['materia_id' => $materia2->id]);

    TutorAgent::fake(['Response']);

    $response = actingAs($this->user)->postJson('/api/v1/tutor/ask', [
        'question' => 'Help me',
        'materia_id' => $materia1->id,
        'topico_id' => $topicoOfMateria2->id,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('topico_id');
});

it('can list tutor conversations', function () {
    TutorAgent::fake(['First response.']);

    actingAs($this->user)->postJson('/api/v1/tutor/ask', [
        'question' => 'First question',
    ]);

    $response = actingAs($this->user)->getJson('/api/v1/tutor/conversations');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data',
            'meta' => [
                'current_page',
                'last_page',
                'per_page',
                'total',
            ],
        ]);
    expect($response->json('data'))->toBeArray();
});

it('cannot access another users conversation', function () {
    $otherUser = User::factory()->create();
    $conversation = AgentConversation::create([
        'id' => (string) Str::uuid(),
        'user_id' => $otherUser->id,
        'title' => 'Other user chat',
    ]);

    TutorAgent::fake(['Nope.']);

    $response = actingAs($this->user)->postJson("/api/v1/tutor/continue/{$conversation->id}", [
        'question' => 'Trying to access your conversation',
    ]);

    $response->assertNotFound();
});

it('requires authentication to ask tutor', function () {
    $response = postJson('/api/v1/tutor/ask', [
        'question' => 'Hello tutor',
    ]);

    $response->assertUnauthorized();
});

it('requires authentication to list conversations', function () {
    $response = getJson('/api/v1/tutor/conversations');

    $response->assertUnauthorized();
});

it('validates question on continue request', function () {
    $conversation = AgentConversation::create([
        'id' => (string) Str::uuid(),
        'user_id' => $this->user->id,
        'title' => 'Chat',
    ]);

    $response = actingAs($this->user)->postJson("/api/v1/tutor/continue/{$conversation->id}", [
        'question' => '',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('question');
});
