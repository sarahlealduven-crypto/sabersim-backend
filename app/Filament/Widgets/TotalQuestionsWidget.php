<?php

namespace App\Filament\Widgets;

use App\Models\Pregunta;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class TotalQuestionsWidget extends BaseWidget
{
    protected ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        $totalQuestions = Pregunta::where('activo', true)->count();

        // Get questions added in last 30 days
        $last30Days = Pregunta::where('activo', true)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        // Get questions added in previous 30 days
        $previous30Days = Pregunta::where('activo', true)
            ->whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])
            ->count();

        // Calculate trend
        $trend = 0;
        if ($previous30Days > 0) {
            $trend = round((($last30Days - $previous30Days) / $previous30Days) * 100, 1);
        }

        $trendIcon = $trend >= 0 ? 'heroicon-o-arrow-up' : 'heroicon-o-arrow-down';
        $trendColor = $trend >= 0 ? 'success' : 'danger';

        // Generate chart data for last 7 days
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $chartData[] = Pregunta::where('activo', true)
                ->whereDate('created_at', $date)
                ->count();
        }

        return [
            BaseWidget\Stat::make('Total Preguntas', $totalQuestions)
                ->description("{$last30Days} nuevas en los últimos 30 días")
                ->descriptionIcon($trendIcon)
                ->chart($chartData)
                ->color('primary'),
        ];
    }

    protected function getColumns(): array|int|null
    {
        return 1;
    }
}
