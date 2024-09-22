<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        return [
            'الكل' => Tab::make(),
            'الطلبات الجديدة' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('order__status_id', 1))->badge(Order::query()->where('order__status_id', 1)->whereHas('orderDetail')->count()),
            'الطلبات المكتملة' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('order__status_id', 2)),
            'غير مكتملة' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('order__status_id', 3))->badge(Order::query()->where('order__status_id', 3)->count()),
        ];
    }
    public function getDefaultActiveTab(): string | int | null
    {
        return 'الكل';
    }
}
