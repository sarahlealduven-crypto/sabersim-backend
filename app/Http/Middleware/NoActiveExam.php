<?php

namespace App\Http\Middleware;

use App\Enums\EstadoExamen;
use App\Models\Examen;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NoActiveExam
{
    /**
     * Handle an incoming request. Block access to the AI tutor when the user has an active exam.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $hasActiveExam = Examen::where('user_id', $request->user()->id)
            ->where('estado', EstadoExamen::EnProgreso)
            ->exists();

        if ($hasActiveExam) {
            abort(403, 'No puedes usar el tutor mientras tienes un examen activo.');
        }

        return $next($request);
    }
}
