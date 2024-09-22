<?php

namespace App\Filament\Resources\PaymentTypeResource\Pages;

use App\Filament\Resources\PaymentTypeResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPaymentType extends EditRecord
{
    protected static string $resource = PaymentTypeResource::class;
    protected function getSavedNotificationMessage():string
    {


        return 'تم تعديل طريقة الدفع بنجاح';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->before(function ($record, $action) {
                // Check if the product has related orders
                if ($record->orders()->exists()) {
                    // Prevent deletion and show a notification
                    Notification::make()
                        ->title('فشل الحذف')
                        ->body('لايمكنك الحذف  لانها مرتبطة بطلبات  ')
                        ->danger()
                        ->send();

                    // Prevent the deletion
                    return $action->halt();
                }
            }),
        ];
    }
}
