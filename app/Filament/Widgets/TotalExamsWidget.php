<?php

namespace App\Filament\Widgets;

use App\Models\Examen;
use App\Enums\EstadoExamen;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class TotalExamsWidget extends BaseWidget
{
    protected ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        $totalExams = Examen::where('estado', EstadoExamen::Completado)->count();

        // Get exams completed in last 7 days
        $last7Days = Examen::where('estado', EstadoExamen::Completado)
            ->where('fecha_completado', '>=', now()->subDays(7))
            ->count();

        // Get exams completed in previous 7 days
        $previous7Days = Examen::where('estado', EstadoExamen::Completado)
            ->whereBetween('fecha_completado', [now()->subDays(14), now()->subDays(7)])
            ->count();

        // Calculate trend
        $trend = 0;
        if ($previous7Days > 0) {
            $trend = round((($last7Days - $previous7Days) / $previous7Days) * 100, 1);
        }

        $trendIcon = $trend >= 0 ? 'heroicon-o-arrow-up' : 'heroicon-o-arrow-down';
        $trendColor = $trend >= 0 ? 'success' : 'danger';

        // Generate chart data for last 7 days
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $chartData[] = Examen::where('estado', EstadoExamen::Completado)
                ->whereDate('fecha_completado', $date)
                ->count();
        }

        return [
            BaseWidget\Stat::make('Total Exámenes', $totalExams)
                ->description("{$last7Days} completados en los últimos 7 días")
                ->descriptionIcon($trendIcon)
                ->chart($chartData)
                ->color('warning'),
        ];
    }

    protected function getColumns(): array|int|null
    {
        return 1;
    }
}
