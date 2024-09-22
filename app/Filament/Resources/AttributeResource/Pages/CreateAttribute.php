<?php

namespace App\Filament\Resources\AttributeResource\Pages;

use App\Filament\Resources\AttributeResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateAttribute extends CreateRecord
{
    protected static string $resource = AttributeResource::class;
    protected function getCreatedNotification(): ?Notification
    {
        $title = 'تم اضافة خاصية بنجاح';

        if (blank($title)) {
            return null;
        }

        return Notification::make()
            ->success()
            ->title($title);
    }
//    protected function getRedirectUrl(): string
//    {
//        $resource = static::getResource();
//
//        return $resource::getUrl('index');
//    }

}
