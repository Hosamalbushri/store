<?php

namespace App\Filament\Resources\AttributeResource\Pages;

use App\Filament\Resources\AttributeResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditAttribute extends EditRecord
{
    protected static string $resource = AttributeResource::class;
    protected function getSavedNotificationMessage():string
    {


        return 'تم تعديل الخاصية بنجاح';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->before(function ($record, $action) {
                // Check if the product has related orders
                if ($record->attributeOptions()->exists()) {
                    // Prevent deletion and show a notification
                    Notification::make()
                        ->title('فشل الحذف')
                        ->body('لايمكنك حذف هذه الخاصية لان لديها خصائص فرعية')
                        ->danger()
                        ->send();

                    // Prevent the deletion
                    return $action->halt();
                }
            }),
        ];
    }
}
