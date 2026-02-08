<?php

namespace App\Ai\Agents;

use App\Models\Materia;
use App\Models\Topico;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Promptable;

#[Provider('openrouter')]
class TutorAgent implements Agent, Conversational
{
    use Promptable, RemembersConversations;

    public function __construct(
        public ?Materia $materia = null,
        public ?Topico $topico = null,
    ) {}

    /**
     * OpenRouter model ID (see https://openrouter.ai/docs#models).
     */
    public function model(): string
    {
        return config('ai.providers.openrouter.model', 'openrouter/free');
    }

    public function instructions(): string
    {
        $base = <<<'INSTRUCTIONS'
        You are a helpful educational tutor for high school students using Sabersim, an online exam practice platform.
        Your role is to help students UNDERSTAND concepts and practice effectively, not to give them direct answers to exam questions.

        Guidelines:
        - Provide hints, explanations, and study strategies.
        - Break down complex topics into simpler parts.
        - Use examples and analogies when helpful.
        - Never give direct answers to specific exam questions or multiple-choice options.
        - Encourage critical thinking with guiding questions.
        - If the student asks for exam answers or the correct option, politely redirect them to understanding the concept so they can reason it out themselves.
        - Respond in the same language the student uses (Spanish or English).
        INSTRUCTIONS;

        if ($this->materia !== null) {
            $base .= "\n\nCurrent subject context:\n";
            $base .= "- Subject: {$this->materia->nombre}\n";
            $base .= '- Description: ' . ($this->materia->descripcion ?? 'N/A') . "\n";
            if ($this->topico !== null) {
                $base .= "- Topic: {$this->topico->nombre}\n";
                $base .= '- Topic description: ' . ($this->topico->descripcion ?? 'N/A') . "\n";
            }
            $base .= "\nFocus your explanations on this subject and topic when relevant.";
        }

        return $base;
    }
}
