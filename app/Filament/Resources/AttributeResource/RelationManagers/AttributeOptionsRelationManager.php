<?php

namespace App\Filament\Resources\AttributeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AttributeOptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'attributeOptions';
    protected static ?string $modelLabel = ' خاصية فرعية';
    protected static ?string $title = 'الخصائص  الفرعية  التابعة لهذه الخاصية';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make()
                    ->schema([
                Forms\Components\TextInput::make('value')->label('اسم الخاصية الفرعية')->placeholder('ادخل اسم الخاصية الفرعية')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                        ]),

                Fieldset::make()
                    ->schema([

                        Forms\Components\Toggle::make('status')->label('الحالة')
                            ->onColor('success')
                            ->offColor('danger')
                        ->default('success'),

                    ])->columnSpan(2),

                Fieldset::make('values')

                    ->label('')
                    ->relationship('values')
                    ->schema([
                        ColorPicker::make('value')->label('اللون')->required()
                            ->hex()->columnSpanFull(),
                    ])
                    ->visibleOn('edit'),




            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('value')->label('اسم الخاصية ')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('status')->label('  الحالة')->onColor('success')
                    ->offColor('danger'),
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
                    if ($record->skus_options()->exists()) {
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
                Tables\Actions\BulkActionGroup::make([]),
            ]);
    }
}
