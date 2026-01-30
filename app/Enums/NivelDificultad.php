<?php

namespace App\Enums;

enum NivelDificultad: string
{
    /**
     * Easy level questions suitable for beginners.
     */
    case Facil = 'facil';

    /**
     * Medium difficulty questions for intermediate learners.
     */
    case Medio = 'medio';

    /**
     * Hard level questions for advanced students.
     */
    case Dificil = 'dificil';
}
