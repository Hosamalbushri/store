<?php

namespace App\Filament\Widgets;

use App\Models\Customer;


use App\Models\Order_Status;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class Order extends BaseWidget
{
    protected function getStats(): array

    {




        return [
            Stat::make('اجمالي العملاء', Customer::count())
                ->description('العملاء')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('الطلبات المكتملة', \App\Models\Order::where('order__status_id',2)->count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->description('الطلبات التي تم تسليمها')
                ->chart([7, 2, 10, 3, 90, 4, 50]),
            Stat::make('الطلبات غير المكتملة', \App\Models\Order::where('order__status_id',3)->count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('danger')
                ->description('الطلبات التي لم يتم تسليمها')
                ->chart([50, 40, 30, 20, 10, 5, 0]),
        ];
    }
}
