<?php

namespace App\Http\Controllers\Api;

use App\Ai\Agents\TutorAgent;
use App\Http\Controllers\Controller;
use App\Http\Requests\TutorAskRequest;
use App\Http\Requests\TutorContinueRequest;
use App\Http\Resources\TutorResponseResource;
use App\Models\AgentConversation;
use App\Models\AgentConversationMessage;
use App\Models\Materia;
use App\Models\Topico;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

#[Group('AI Tutor', weight: 5)]
class TutorController extends Controller
{
    /**
     * Start a new conversation with the AI tutor.
     */
    #[Endpoint(
        operationId: 'askTutor',
        title: 'Preguntar al tutor AI',
        description: 'Inicia una nueva conversación con el tutor AI. El tutor ayuda a entender conceptos y practicar, sin dar respuestas directas a exámenes. Opcionalmente puedes indicar materia y tema para un contexto más enfocado.'
    )]
    #[Response(200, 'Respuesta del tutor')]
    #[Response(401, 'No autenticado')]
    #[Response(403, 'Usuario tiene un examen activo')]
    #[Response(422, 'Validación fallida')]
    public function ask(TutorAskRequest $request): TutorResponseResource
    {
        $user = $request->user();
        $materia = $request->filled('materia_id')
            ? Materia::find($request->input('materia_id'))
            : null;
        $topico = $request->filled('topico_id')
            ? Topico::find($request->input('topico_id'))
            : null;

        $agent = new TutorAgent($materia, $topico);
        $response = $agent->forUser($user)->prompt($request->input('question'));

        $data = [
            'conversation_id' => $response->conversationId,
            'response' => $response->text,
            'materia' => $materia ? ['id' => $materia->id, 'nombre' => $materia->nombre] : null,
            'topico' => $topico ? ['id' => $topico->id, 'nombre' => $topico->nombre] : null,
            'created_at' => now()->toIso8601String(),
        ];

        return new TutorResponseResource($data);
    }

    /**
     * Continue an existing tutor conversation.
     */
    #[Endpoint(
        operationId: 'continueTutorConversation',
        title: 'Continuar conversación con el tutor',
        description: 'Envía una nueva pregunta dentro de una conversación existente con el tutor AI. La conversación debe pertenecer al usuario autenticado.'
    )]
    #[PathParameter('conversationId', description: 'ID de la conversación (UUID)', type: 'string', example: '01936e42-7a1b-7000-8000-000000000000')]
    #[Response(200, 'Respuesta del tutor')]
    #[Response(401, 'No autenticado')]
    #[Response(403, 'No autorizado para esta conversación o examen activo')]
    #[Response(404, 'Conversación no encontrada')]
    #[Response(422, 'Validación fallida')]
    public function continue(string $conversationId, TutorContinueRequest $request): TutorResponseResource
    {
        $user = $request->user();

        $conversation = AgentConversation::query()
            ->where('id', $conversationId)
            ->where('user_id', $user->id)
            ->first();

        if (! $conversation) {
            abort(404, 'Conversación no encontrada.');
        }

        $agent = new TutorAgent;
        $response = $agent->continue($conversationId, as: $user)->prompt($request->input('question'));

        $data = [
            'conversation_id' => $conversationId,
            'response' => $response->text,
            'materia' => null,
            'topico' => null,
            'created_at' => now()->toIso8601String(),
        ];

        return new TutorResponseResource($data);
    }

    /**
     * List the authenticated user's tutor conversations.
     */
    #[Endpoint(
        operationId: 'listTutorConversations',
        title: 'Listar conversaciones del tutor',
        description: 'Devuelve la lista paginada de conversaciones del usuario con el tutor AI, ordenadas por la más reciente.'
    )]
    #[Response(200, 'Lista de conversaciones del tutor')]
    #[Response(401, 'No autenticado')]
    public function conversations(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $agentClass = TutorAgent::class;

        $conversationIds = AgentConversationMessage::query()
            ->where('user_id', $userId)
            ->where('agent', $agentClass)
            ->distinct()
            ->pluck('conversation_id');

        $conversations = AgentConversation::query()
            ->whereIn('id', $conversationIds)
            ->where('user_id', $userId)
            ->orderByDesc('updated_at')
            ->paginate(15);

        return response()->json([
            'data' => $conversations->items(),
            'meta' => [
                'current_page' => $conversations->currentPage(),
                'last_page' => $conversations->lastPage(),
                'per_page' => $conversations->perPage(),
                'total' => $conversations->total(),
            ],
        ]);
    }
}
