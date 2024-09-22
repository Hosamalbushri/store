<?php

namespace App\Filament\Resources\OrderStatusResource\Pages;

use App\Filament\Resources\OrderStatusResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditOrderStatus extends EditRecord
{
    protected static string $resource = OrderStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->before(function ($record, $action) {
                // Check if the product has related orders
                if ($record->orders()->exists()) {
                    // Prevent deletion and show a notification
                    Notification::make()
                        ->title('فشل الحذف')
                        ->body('لايمكنك حذف هذه الحالة لانها مرتبطة بطلبات  ')
                        ->danger()
                        ->send();

                    // Prevent the deletion
                    return $action->halt();
                }
            }),
        ];
    }
}
