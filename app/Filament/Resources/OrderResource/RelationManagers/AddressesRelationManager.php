<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';
    protected static ?string $modelLabel = 'عناوين المستخدم';
    protected static ?string $title = 'العناوين التابعة لهذا المستخدم';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }
    public  function  infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                \Filament\Infolists\Components\Section::make('معلومات المستلم')->schema([
                    TextEntry::make('firstname')->label('اسم المستلم ')->color('danger')->getStateUsing(fn ($record) => $record->firstname .'  '.$record->lastname),
                    TextEntry::make('phone')->label('رقم الهاتف')->color('danger'),
                    TextEntry::make('country')->label('الدولة')->color('danger'),
                    TextEntry::make('city')->label('المدينة')->color('danger'),
                    TextEntry::make('address')->label('العنوان')->color('danger'),
                    TextEntry::make('neighborhood')->label('الحي السكني')->color('danger'),
                ])->columns(3),



            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('معلومات المستلم')
            ->columns([
                Tables\Columns\TextColumn::make('firstname')->label('الاسم الكامل')->searchable()->getStateUsing(fn ($record) => $record->firstname .'  '.$record->lastname)->color('danger'),
                Tables\Columns\TextColumn::make('phone')->label('رقم الهاتف')->searchable(),
                ])

            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ]);
    }

    public  function canCreate(): bool
    {
        return false;
    }
    public  function canDelete(Model $record): bool
    {
        return false;
    }
    public  function canEdit(Model $record): bool
    {
        return false;
    }
}
