<?php

namespace App\Filament\Widgets;

use App\Models\Examen;
use App\Enums\EstadoExamen;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class AverageScoreWidget extends BaseWidget
{
    protected ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        // Calculate average score from completed exams
        $averageScore = Examen::where('estado', EstadoExamen::Completado)
            ->avg('puntaje_total') ?? 0;

        $averageScoreFormatted = number_format($averageScore, 2);

        // Get average from last month
        $lastMonth = Examen::where('estado', EstadoExamen::Completado)
            ->where('fecha_completado', '>=', now()->subMonth())
            ->avg('puntaje_total') ?? 0;

        // Get average from previous month
        $previousMonth = Examen::where('estado', EstadoExamen::Completado)
            ->whereBetween('fecha_completado', [now()->subMonths(2), now()->subMonth()])
            ->avg('puntaje_total') ?? 0;

        // Calculate trend
        $trend = 0;
        if ($previousMonth > 0) {
            $trend = round(($lastMonth - $previousMonth), 2);
        }

        $trendIcon = $trend >= 0 ? 'heroicon-o-arrow-up' : 'heroicon-o-arrow-down';
        $trendColor = $trend >= 0 ? 'success' : 'danger';
        $trendDescription = $trend >= 0 ? "+{$trend}%" : "{$trend}%";

        return [
            BaseWidget\Stat::make('Puntaje Promedio', "{$averageScoreFormatted}%")
                ->description("{$trendDescription} vs mes anterior")
                ->descriptionIcon($trendIcon)
                ->color('info'),
        ];
    }

    protected function getColumns(): array|int|null
    {
        return 1;
    }
}
