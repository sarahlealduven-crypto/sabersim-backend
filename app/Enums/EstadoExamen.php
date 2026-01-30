<?php

namespace App\Enums;

enum EstadoExamen: string
{
    /**
     * Exam is currently being taken by the user.
     */
    case EnProgreso = 'en_progreso';

    /**
     * Exam has been finished, scored, and completed successfully.
     */
    case Completado = 'completado';

    /**
     * Exam was abandoned by the user before completion.
     */
    case Abandonado = 'abandonado';
}
