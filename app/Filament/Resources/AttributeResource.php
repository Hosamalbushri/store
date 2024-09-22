<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttributeResource\Pages;
use App\Filament\Resources\AttributeResource\RelationManagers;
use App\Filament\Resources\AttributeResource\RelationManagers\AttributeOptionsRelationManager;
use App\Models\Attribute;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttributeResource extends Resource
{
    protected static ?string $modelLabel = 'خاصية';
    protected static ?string $pluralLabel = 'خصائص';

    protected static ?string $model = Attribute::class;
    protected static ?string $navigationGroup = 'تفاصيل المنتجات';
    public static ?string $navigationParentItem = 'المنتجات';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';
    protected static ?string $navigationLabel = 'الخصائص';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')->label('اسم الخاصية')->placeholder('ادخل  اسم الخاصية')->required()->unique(ignoreRecord: true)
                            ->maxLength(255),


                    ]),
                Fieldset::make()
                    ->schema([

                        Forms\Components\Toggle::make('status')->label('الحالة')
                            ->onColor('success')
                            ->offColor('danger')
                            ->default('success'),

                    ])->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('اسم الخاصية ')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('status')->label('الحالة')->onColor('success')->offColor('danger'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->before(function ($record, $action) {
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->before(function ($record, $action) {
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
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AttributeOptionsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttributes::route('/'),
            'create' => Pages\CreateAttribute::route('/create'),
            'edit' => Pages\EditAttribute::route('/{record}/edit'),
        ];
    }
}
