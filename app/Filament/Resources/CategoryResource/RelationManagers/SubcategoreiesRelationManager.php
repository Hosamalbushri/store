<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubcategoreiesRelationManager extends RelationManager
{
    protected static string $relationship = 'Subcategoreies';
    protected static ?string $modelLabel = ' الاقسام الفرعية';
    protected static ?string $title = 'الاقسام  الفرعية  التابعة لهذه القسم';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')->label('اسم القسم')->placeholder('ادخل  اسم القسم')->required()
                            ->maxLength(255),

                    ]),
                Fieldset::make()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('photo')->label('صورة القسم')->placeholder('اختر صورة القسم')
                            ->disk('public')
                            ->image()
                            ->conversion('thumb')

                        ,

                    ]),
                Fieldset::make()
                    ->schema([
                        Forms\Components\Toggle::make('status')->label('الحالة')
                            ->onColor('success')->default('success')
                            ->offColor('danger'),

                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('الاسم')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('status')->label('الحالة')->onColor('success')->offColor('danger'),
                Tables\Columns\ImageColumn::make('photo')->label('الصورة'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->before(function ($record, $action) {
                    // Check if the product has related orders
                    if ($record->products()->exists()) {
                        // Prevent deletion and show a notification
                        Notification::make()
                            ->title('فشل الحذف')
                            ->body('لا يمكنك حذف هذه القسم لان لدية منتجات مرتبط بة ')
                            ->danger()
                            ->send();

                        // Prevent the deletion
                        return $action->halt();
                    }
                }),
            ])
            ->bulkActions([
            ]);
    }
}
