<?php

namespace App\Filament\Resources\CustomerResource\Widgets;

use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CustomerWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
//            Stat::make('اجمالي العملاء',Customer::count()),
        ];
    }
}
