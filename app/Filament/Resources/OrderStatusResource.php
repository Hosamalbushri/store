<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderStatusResource\Pages;
use App\Models\Order_Status;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class OrderStatusResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Order_Status::class;
    protected static ?string $modelLabel = 'حالة طلب';
    protected static ?string $pluralLabel = 'حالات الطلبات';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'تفاصيل الطلبات';

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $navigationLabel = 'حالات الطلبات';
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'update',
        ];
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('status_name')->label('حالة الطلب')->unique(ignoreRecord: true)
                    ->required(fn (string $operation): bool => $operation === 'create')

                ,
                Forms\Components\Select::make('status_color')->label('اللون')->required()
                ->options([
                    'danger'=>'احمر',
                    'info'=>'ازرق',
                    'success'=>'اخضر',
                    'warning'=>'اصفر'
                ])


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('status_name')->color(fn($record)=>$record->status_color)->label('الحالة')->searchable(),
                     IconColumn::make('id')
                         ->label(' ')
                         ->options([
                             'heroicon-o-check-circle' => fn($record) => $record->id === 2,
                             'heroicon-o-x-circle' => fn($record) => $record->id === 3,
                             'heroicon-o-clock' => fn($record) => $record->id === 1,
                         ])
                         ->colors(function ( $record){
                             $color1= Order_Status::find(1);
                             $color2= Order_Status::find(2);
                             $color3= Order_Status::find(3);
                             $color1=$color1->status_color;
                             $color2=$color2->status_color;
                             $color3=$color3->status_color;
                             return
                             [
                                 $color1=> fn($record) => $record->id === 1,
                                 $color2=> fn($record) => $record->id === 2,
                                 $color3=> fn($record) => $record->id === 3,
                             ];
                         }),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->before(function ($record, $action) {
                    // Check if the product has related orders
                    if ($record->orders()->exists()) {
                        // Prevent deletion and show a notification
                        Notification::make()
                            ->title('فشل الحذف')
                            ->body('لايمكنك حذف هذه الحالة لانها مرتبطة بطلبات  ')
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrderStatuses::route('/'),
            'create' => Pages\CreateOrderStatus::route('/create'),
            'edit' => Pages\EditOrderStatus::route('/{record}/edit'),
        ];


    }
    public static function canCreate(): bool
    {
        return false;
    }
    public static function canDelete(Model $record): bool
    {
        return false;
    }
}
