<?php

use App\Ai\Agents\TutorAgent;
use App\Ai\Storage\SpanishTitleConversationStore;
use App\Models\Materia;
use App\Models\Topico;
use Illuminate\Support\Collection;
use Laravel\Ai\Contracts\ConversationStore;
use Laravel\Ai\Contracts\Gateway\TextGateway;
use Laravel\Ai\Contracts\Providers\TextProvider;
use Laravel\Ai\Gateway\TextGenerationOptions;
use Laravel\Ai\Prompts\AgentPrompt;
use Laravel\Ai\Responses\AgentResponse;
use Laravel\Ai\Responses\Data\Meta;
use Laravel\Ai\Responses\Data\Usage;
use Laravel\Ai\Responses\StreamableAgentResponse;
use Laravel\Ai\Responses\TextResponse;

function fakeConversationStore(?string &$storedTitle = null): ConversationStore
{
    return new class($storedTitle) implements ConversationStore
    {
        private ?string $storedTitle;

        public function __construct(?string &$storedTitle)
        {
            $this->storedTitle = &$storedTitle;
        }

        public function latestConversationId(int $userId): ?string
        {
            return "latest-{$userId}";
        }

        public function storeConversation(int $userId, string $title): string
        {
            $this->storedTitle = $title;

            return "conversation-{$userId}";
        }

        public function storeUserMessage(string $conversationId, int $userId, AgentPrompt $prompt): string
        {
            return "{$conversationId}-user-{$userId}";
        }

        public function storeAssistantMessage(string $conversationId, int $userId, AgentPrompt $prompt, AgentResponse $response): string
        {
            return "{$conversationId}-assistant-{$userId}";
        }

        public function getLatestConversationMessages(string $conversationId, int $limit): Collection
        {
            return collect([$conversationId, $limit]);
        }
    };
}

function fakeTextProvider(TextGateway $gateway): TextProvider
{
    return new class($gateway) implements TextProvider
    {
        public function __construct(private TextGateway $gateway) {}

        public function prompt(AgentPrompt $prompt): AgentResponse
        {
            return new AgentResponse('invocation', 'response', new Usage, new Meta);
        }

        public function stream(AgentPrompt $prompt): StreamableAgentResponse
        {
            throw new RuntimeException('Streaming is not used by this test.');
        }

        public function textGateway(): TextGateway
        {
            return $this->gateway;
        }

        public function useTextGateway(TextGateway $gateway): self
        {
            $this->gateway = $gateway;

            return $this;
        }

        public function defaultTextModel(): string
        {
            return 'default-model';
        }

        public function cheapestTextModel(): string
        {
            return 'cheap-model';
        }

        public function smartestTextModel(): string
        {
            return 'smart-model';
        }
    };
}

function fakeTextGateway(string|Throwable $result): TextGateway
{
    return new class($result) implements TextGateway
    {
        public function __construct(private string|Throwable $result) {}

        public function generateText(
            TextProvider $provider,
            string $model,
            ?string $instructions,
            array $messages = [],
            array $tools = [],
            ?array $schema = null,
            ?TextGenerationOptions $options = null,
            ?int $timeout = null,
        ): TextResponse {
            if ($this->result instanceof Throwable) {
                throw $this->result;
            }

            return new TextResponse($this->result, new Usage, new Meta('fake', $model));
        }

        public function streamText(
            string $invocationId,
            TextProvider $provider,
            string $model,
            ?string $instructions,
            array $messages = [],
            array $tools = [],
            ?array $schema = null,
            ?TextGenerationOptions $options = null,
            ?int $timeout = null,
        ): Generator {
            if (false) {
                yield null;
            }
        }

        public function onToolInvocation(Closure $invoking, Closure $invoked): self
        {
            return $this;
        }
    };
}

it('builds tutor instructions with optional subject and topic context', function (): void {
    config(['ai.providers.openrouter.model' => 'openrouter/test-model']);

    $materia = Materia::factory()->create([
        'nombre' => 'Matemáticas',
        'descripcion' => 'Álgebra y geometría',
    ]);
    $topico = Topico::factory()->create([
        'materia_id' => $materia->id,
        'nombre' => 'Funciones',
        'descripcion' => 'Funciones lineales',
    ]);

    $agent = new TutorAgent($materia, $topico);
    $instructions = $agent->instructions();

    expect($agent->model())->toBe('openrouter/test-model')
        ->and($instructions)->toContain('respond in Spanish')
        ->and($instructions)->toContain('Subject: Matemáticas')
        ->and($instructions)->toContain('Description: Álgebra y geometría')
        ->and($instructions)->toContain('Topic: Funciones')
        ->and($instructions)->toContain('Topic description: Funciones lineales')
        ->and((new TutorAgent)->instructions())->not->toContain('Current subject context');
});

it('binds the custom Spanish title conversation store in the container', function (): void {
    expect(app(ConversationStore::class))->toBeInstanceOf(SpanishTitleConversationStore::class);
});

it('stores translated Spanish conversation titles and delegates store calls', function (): void {
    $storedTitle = null;
    $store = new SpanishTitleConversationStore(
        fakeConversationStore($storedTitle),
        fakeTextProvider(fakeTextGateway('Título en español')),
    );

    expect($store->latestConversationId(5))->toBe('latest-5')
        ->and($store->storeConversation(5, 'A very long English title'))->toBe('conversation-5')
        ->and($storedTitle)->toBe('Título en español')
        ->and($store->getLatestConversationMessages('conversation-5', 3)->all())->toBe(['conversation-5', 3]);
});

it('falls back to the original limited title when translation fails or is blank', function (): void {
    $failedTitle = null;
    $blankTitle = null;
    $longTitle = str_repeat('Long title ', 30);

    $failedStore = new SpanishTitleConversationStore(
        fakeConversationStore($failedTitle),
        fakeTextProvider(fakeTextGateway(new RuntimeException('provider down'))),
    );
    $blankStore = new SpanishTitleConversationStore(
        fakeConversationStore($blankTitle),
        fakeTextProvider(fakeTextGateway('   ')),
    );

    $failedStore->storeConversation(1, $longTitle);
    $blankStore->storeConversation(1, 'Short title');

    expect(strlen($failedTitle))->toBeLessThanOrEqual(103)
        ->and($failedTitle)->toStartWith('Long title')
        ->and($blankTitle)->toBe('Short title');
});
