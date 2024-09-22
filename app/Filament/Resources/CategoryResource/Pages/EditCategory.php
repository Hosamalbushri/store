<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;
    protected function getSavedNotificationMessage():string
    {


        return 'تم تعديل القسم بنجاح';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->before(function ($record, $action) {
                // Check if the product has related orders
                if ($record->Subcategoreies()->exists()) {
                    // Prevent deletion and show a notification
                    Notification::make()
                        ->title('فشل الحذف')
                        ->body('لايمكنك حذف هذه القسم لان لدية اقسام فرعية')
                        ->danger()
                        ->send();

                    // Prevent the deletion
                    return $action->halt();
                }
            })

        ];
    }
}
