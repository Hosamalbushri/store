<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

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
            'الاكثر نشاطا' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('orders')),
            'الاقل نشاطا' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDoesntHave('orders')),
            'المفعلين' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 1))->badge(Customer::query()->where('status', 1)->count()),

            'الموقفين' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 0))->badge(Customer::query()->where('status', 0)->count()),

        ];
    }
}
