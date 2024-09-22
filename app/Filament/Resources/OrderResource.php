<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\AttributeOption;
use App\Models\Order;
use App\Models\Order_Details;
use App\Models\Order_Status;
use App\Models\Sku;
use App\Traits\DisablesNotifications;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Infolists\Components\Card;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use League\Uri\Contracts\QueryInterface;

class OrderResource extends Resource implements HasShieldPermissions
{
    use DisablesNotifications;

    protected static ?string $model = Order::class;
    protected static ?string $modelLabel = 'طلب';
    protected static ?string $pluralLabel = 'الطلبات';
    protected static ?string $navigationGroup = 'تفاصيل الطلبات';
    protected static ?int $navigationSort = -30;
    protected static ?string $navigationIcon = 'heroicon-s-shopping-cart';
    protected static ?string $navigationLabel = 'الطلبات';
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
                Forms\Components\Select::make('order__status_id')->label('حالة الطلب')->required()
                    ->options(Order_Status::all()->where('id','!=',1)->pluck('status_name', 'id')->toArray())
            ]);
    }
    public static function  infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                Section::make('معلومات الطلب')->schema([
                    TextEntry::make('order_number')->label('رقم الطلب')->color('danger'),
                    TextEntry::make('order_date')->label('تاريخ الطلب')->color('danger'),
                    TextEntry::make('customer.name')->label('اسم العميل ')->color('info'),
                    TextEntry::make('customer.email')->label('حساب العميل')->color('danger'),
                    TextEntry::make('payment_type.name')->label('طريقة الدفع')->color('danger'),
                    TextEntry::make('status.status_name')->label('حالة  الطلب')->color(fn($record)=>$record->status->status_color),

                ])->columns(4),
                Card::make('مستلم الطلب')
                    ->schema([
                        TextEntry::make('userWhoChangedStatus.name')->label('اسم المستخدم')->color('danger'),
                        TextEntry::make('status_changed_at')->label('تاريخ التعديل')->color('danger')->date('d/m/Y'),

                    ])->columns(2)->hidden(fn ($record) => empty($record->userWhoChangedStatus->name)),
                Section::make('معلومات المستلم')->schema([
                    TextEntry::make('customer_address.firstname')->label('اسم المستلم ')->color('danger')->getStateUsing(fn ($record) => $record->customer_address->firstname .'  '.$record->customer_address->lastname)->columnSpan(1),
                    TextEntry::make('customer_address.phone')->label('رقم الهاتف')->color('danger'),
                    TextEntry::make('customer_address.country')->label('الدولة')->color('danger'),
                    TextEntry::make('customer_address.city')->label('المدينة')->color('danger'),
                    TextEntry::make('customer_address.address')->label('العنوان')->color('danger'),
                    TextEntry::make('customer_address.neighborhood')->label('الحي السكني')->color('danger')->columnSpan(2),
                ])->columns(3),



                \Filament\Infolists\Components\Section::make('تفاصيل الطلب')->schema([
                    Fieldset::make('معلومات المنتج')->hiddenLabel()->schema([
                    RepeatableEntry::make('orderDetail')->label('')->hiddenLabel()->contained()
                        ->schema([


                                TextEntry::make('sku.product.name')->label('اسم المنتج')->color('danger')->columnSpan(2),
                                TextEntry::make('sku.price')->label('سعر المنتج')->color('danger')->money('YER')->columnSpan(2),
                                TextEntry::make('quantity')->label('كمية المنتج')->color('danger')->numeric()->columnSpan(2),
                                TextEntry::make('price')->label('السعر قبل الخصم ')->color('danger')->money('YER')->columnSpan(2),
                                TextEntry::make('discount')->label('قيمة الخصم')->color('danger')->money('YER')->columnSpan(2),
                                TextEntry::make('total_price')->label('السعر بعد الخصم')->money('YER')->color('danger')->columnSpan(2),
                            RepeatableEntry::make('attributes')->schema([
                                TextEntry::make('value')->label('اسم المنتج')->color('danger')->color('info')->label(function (AttributeOption $record) {
                                    $attribute = $record->attribute->name;
                                    return 'مع '. $attribute;}),
                                ColorEntry::make('values.value')->hiddenLabel()->hidden(fn ($record) => empty($record->values->value)),

                            ])->grid(7)->hiddenLabel()->columnSpanFull(),

                            ])->columns(12)->columnSpan(12),
                        ])->grow(),

                    Card::make('اجمالي الطلب')->schema([
                            TextEntry::make('price')->label('اجمالي الطلب')->money('YER')->color('danger')->getStateUsing(fn ($record) => $record->total_price)->hiddenLabel(),
                    ])->hiddenLabel()->inlineLabel(),

                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')->label('رقم الطلب')->searchable(),
                Tables\Columns\TextColumn::make('order_date')->label('تاريخ الطلب')->searchable(),
                Tables\Columns\TextColumn::make('customer.name')->label('اسم العميل')->searchable(),
                Tables\Columns\TextColumn::make('customer.email')->label('حساب العميل')->searchable(),

                IconColumn::make('status.id')
                    ->label('حالة الطلب')
                    ->options([
                        'heroicon-o-check-circle' => fn($record) => $record->status->id === 2,
                        'heroicon-o-x-circle' => fn($record) => $record->status->id === 3,
                        'heroicon-o-clock' => fn($record) => $record->status->id === 1,
                    ])
                    ->colors(function (){
                        $color1= Order_Status::find(1);
                        $color2= Order_Status::find(2);
                        $color3= Order_Status::find(3);
                        $color1=$color1->status_color;
                        $color2=$color2->status_color;
                        $color3=$color3->status_color;
                        return
                            [
                                $color1=> fn($record) => $record->status->id === 1,
                                $color2=> fn($record) => $record->status->id === 2,
                                $color3=> fn($record) => $record->status->id === 3,
                            ];
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
            ])
           ;
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
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
    public static function query(): Builder
    {
        return parent::query();
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('orderDetail');
    }
    public static function getGloballySearchableAttributes(): array
    {
        return ['order_number'];
    }

}
