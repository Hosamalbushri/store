<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Filament\Resources\ProductResource\RelationManagers\SkusRelationManager;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class EditProduct extends EditRecord
{

    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()->before(function ($record, $action) {
                // Check if the product has related orders
                if ($record->skus()->exists()||$record->favorite()->exists()) {
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
    public function getRelationManagers(): array
    {
        return [
            SkusRelationManager::class,
        ];
    }






}
