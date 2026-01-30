<?php

namespace App\Enums;

enum TipoExamen: string
{
    /**
     * Complete exam covering all active subjects.
     */
    case Completo = 'completo';

    /**
     * Exam focused on a specific subject only.
     */
    case PorMateria = 'por_materia';
}
