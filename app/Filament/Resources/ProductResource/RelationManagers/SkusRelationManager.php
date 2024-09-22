<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;
use App\Filament\Resources\ProductResource;
use App\Models\Sku;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;


class SkusRelationManager extends RelationManager
{
    protected static string $resource = ProductResource::class;
    protected static string $relationship = 'skus';
    protected static ?string $modelLabel = 'خصائص المنتج';
    protected static ?string $title = 'الخصائص التابعة لهذا المنتج';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('معلومات المنتج')
                    ->schema([

                        Forms\Components\TextInput::make('quantity')
                            ->label('الكمية')->required()->numeric()->minValue(1)
                        ,
                        Forms\Components\TextInput::make('price')->label('سعر المنتج')->placeholder('ادخل  سعر المنتج')->required()
                            ->numeric()
                            ->prefix('ريال')
                            ->minValue(1)
                            ->maxLength(255),


                        Forms\Components\TextInput::make('code')->label('كود المنتج')->placeholder('ادخل  كود المنتج')->disabled()->hiddenOn('create'),

                    ]),
                Fieldset::make()
                    ->schema([

                        Forms\Components\Select::make('attribute_Options')->label('الصفات الفرعية')
                            ->multiple()
                            ->relationship('attribute_Options', 'value')
                            ->preload()

                    ])->visibleOn('edit'),

            ]);

    }




    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('price')->label('السعر')->numeric()->suffix(' ريال'),
                Tables\Columns\TextColumn::make('quantity')->label('الكمية')->numeric(),
                Tables\Columns\TextColumn::make('code')->label('كود المنتج')->searchable()->color('danger'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('اضافة خصم')
                    ->color('info')
                    ->icon('heroicon-o-tag')
                    ->label('اضافة خصم')
                    ->form(function (Sku $record){
                        return[
                        TextInput::make('discount')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->label('قيمة الخصم')->prefix('%')
                            ->default($record->discount ? $record->discount->value : null),
                        TextInput::make('discounted_price')->label('السعر بعد الخصم')
                            ->disabled()
                        ->default($record->discount ? $record->discount->discounted_price : null)->hidden(fn ($record) => empty($record->discount->discounted_price)),

                    ];})
                    ->action(function (Sku $record, array $data): void {
                        // Check if the product already has a discount
                        if ($record->discount) {
                            // Update the existing discount
                            $record->discount->update([
                                'value' => $data['discount'],
                                'discounted_price'=>  $record->price - ($record->price * ($data['discount'] / 100))

                            ]);
                        } else {
                            // Create a new discount
                            $record->discount()->create([
                                'value' => $data['discount'],
                                'discounted_price'=>  $record->price - ($record->price * ($data['discount'] / 100))

                            ]);


                        }
                        Notification::make()
                            ->title('تم الاضافة بنجاح')
                            ->success()
                            ->body('تم اضافة الخصم لهذا المنتج ')
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make()->before(function ($record, $action) {
                    // Check if the product has related orders
                    if ($record->orders()->exists()) {
                        // Prevent deletion and show a notification
                        Notification::make()
                            ->title('فشل الحذف')
                            ->body('لايمكنك الحذف لانها مرتبطة بطلبات  ')
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
