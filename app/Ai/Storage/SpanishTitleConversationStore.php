<?php

namespace App\Ai\Storage;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Ai\Contracts\ConversationStore;
use Laravel\Ai\Contracts\Providers\TextProvider;
use Laravel\Ai\Messages\UserMessage;
use Laravel\Ai\Prompts\AgentPrompt;
use Laravel\Ai\Responses\AgentResponse;
use Throwable;

class SpanishTitleConversationStore implements ConversationStore
{
    public function __construct(
        protected ConversationStore $store,
        protected TextProvider $textProvider,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function latestConversationId(int $userId): ?string
    {
        return $this->store->latestConversationId($userId);
    }

    /**
     * Store a new conversation with a Spanish title.
     */
    public function storeConversation(int $userId, string $title): string
    {
        $spanishTitle = $this->titleToSpanish($title);

        return $this->store->storeConversation($userId, $spanishTitle);
    }

    /**
     * {@inheritDoc}
     */
    public function storeUserMessage(string $conversationId, int $userId, AgentPrompt $prompt): string
    {
        return $this->store->storeUserMessage($conversationId, $userId, $prompt);
    }

    /**
     * {@inheritDoc}
     */
    public function storeAssistantMessage(string $conversationId, int $userId, AgentPrompt $prompt, AgentResponse $response): string
    {
        return $this->store->storeAssistantMessage($conversationId, $userId, $prompt, $response);
    }

    /**
     * {@inheritDoc}
     *
     * @return \Illuminate\Support\Collection<int, \Laravel\Ai\Messages\Message>
     */
    public function getLatestConversationMessages(string $conversationId, int $limit): Collection
    {
        return $this->store->getLatestConversationMessages($conversationId, $limit);
    }

    /**
     * Translate the conversation title to Spanish.
     */
    protected function titleToSpanish(string $title): string
    {
        try {
            $response = $this->textProvider->textGateway()->generateText(
                $this->textProvider,
                $this->textProvider->cheapestTextModel(),
                'Traduce a español en 3-5 palabras. Responde solo con el título, sin comillas ni puntuación.',
                [new UserMessage(Str::limit($title, 500))],
            );

            $result = trim($response->text);
            if ($result !== '') {
                return Str::limit($result, 100);
            }
        } catch (Throwable) {
            // fall through to fallback
        }

        return Str::limit($title, 100, preserveWords: true);
    }
}
