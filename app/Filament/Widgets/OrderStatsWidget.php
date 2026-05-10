<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Pesanan Hari Ini', Order::whereDate('created_at', today())->count())
                ->description('Total masuk hari ini')
                ->color('primary'),

            Stat::make('Menunggu Konfirmasi', Order::where('status', 'pending')->count())
                ->description('Perlu segera diproses')
                ->color('warning'),

            Stat::make(
                'Pendapatan Bulan Ini',
                'Rp ' . number_format(
                    Order::whereMonth('created_at', now()->month)
                        ->whereNotIn('status', ['cancelled'])
                        ->sum('total_amount'),
                    0, ',', '.'
                )
            )
                ->description('Semua order aktif bulan ini')
                ->color('success'),

            Stat::make('Order Selesai',
                Order::where('status', 'done')
                    ->whereMonth('created_at', now()->month)
                    ->count()
            )
                ->description('Bulan ini')
                ->color('info'),
        ];
    }
}
