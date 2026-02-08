<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class TotalStudentsWidget extends BaseWidget
{
    protected ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        $totalStudents = User::count();

        // Get users created in last 7 days
        $last7Days = User::where('created_at', '>=', now()->subDays(7))->count();

        // Get users created in previous 7 days
        $previous7Days = User::whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])->count();

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
            $chartData[] = User::whereDate('created_at', $date)->count();
        }

        return [
            BaseWidget\Stat::make('Total Estudiantes', $totalStudents)
                ->description("{$last7Days} nuevos en los últimos 7 días")
                ->descriptionIcon($trendIcon)
                ->chart($chartData)
                ->color('success'),
        ];
    }

    protected function getColumns(): array|int|null
    {
        return 1;
    }
}
