<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Traits\DisablesNotifications;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    use DisablesNotifications;

    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];

    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        try {
            if ($data['order__status_id']==2){
                if ($this->record->order__status_id==1||$this->record->order__status_id==3)
                {
                    $this->record->checkSkuQuantities();
                }
                Notification::make()
                    ->title('تم استلام الطلب بنجاح')
                    ->success()
                    ->send();
            }
            if ($data['order__status_id']==3)
            {
                if ($this->record->order__status_id==1||$this->record->order__status_id==2)
                {
                    $this->record->UpdateSkuQuantities();
                }
                Notification::make()
                    ->title('تم تحديث حالة الطلب بنجاح')
                    ->success()
                    ->send();
            }



        } catch (\Exception $e) {
            // Send a notification about the error
            Notification::make()
                ->title('Error')
                ->body($e->getMessage())
                ->danger()
                ->send();
            $data['order__status_id']=3;


            // Redirect to the index page of the resource after encountering an error}
            return $data;

        }
        return $data;
    }

    protected function getRedirectUrl(): string {
        return static::getResource()::getUrl('view',  [
            'record' => $this->record
        ]);
    }
    protected function getSavedNotification():?Notification
    {
        return null;

    }


}
