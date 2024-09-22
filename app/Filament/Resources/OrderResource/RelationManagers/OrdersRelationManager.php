<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';
    protected static ?string $modelLabel = 'طلبات المستخدم';
    protected static ?string $title = 'الطلبات التابعة لهذا المستخدم';

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
                    TextEntry::make('customer_address.firstname')->label('اسم المستلم ')->color('danger'),
                    TextEntry::make('customer_address.phone')->label('رقم الهاتف')->color('danger'),
                    TextEntry::make('customer_address.country')->label('الدولة')->color('danger'),
                    TextEntry::make('customer_address.city')->label('المدينة')->color('danger'),
                    TextEntry::make('customer_address.address')->label('العنوان')->color('danger'),
                    TextEntry::make('customer_address.neighborhood')->label('الحي السكني')->color('danger'),
                ])->columns(3),



               Section::make('تفاصيل الطلب')->schema([
                    RepeatableEntry::make('orderDetail')->label('')->hiddenLabel()->contained()
                        ->schema([
                                TextEntry::make('sku.product.name')->label('اسم المنتج')->color('danger'),
                                TextEntry::make('sku.price')->label('سعر المنتج')->color('danger')->money('YER'),
                                TextEntry::make('quantity')->label('كمية المنتج')->color('danger')->numeric(),
                                TextEntry::make('price')->label('السعر قبل الخصم ')->color('danger')->money('YER'),
                                TextEntry::make('discount')->label('قيمة الخصم')->color('danger')->money('YER'),
                                TextEntry::make('total_price')->label('السعر بعد الخصم')->money('YER')->color('danger'),


                        ])->columnSpan(6)->columns(6),

                    Group::make()->schema([
                        TextEntry::make('price')->label('اجمالي الطلب')->money('YER')->color('danger')->getStateUsing(fn ($record) => $record->total_price)->columns(2),
                    ])->columns(1)->columnSpan(1),

                ])
            ]);
    }
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('order_number')->label('رقم الطلب')->searchable(),
                Tables\Columns\TextColumn::make('order_date')->label('تاريخ الطلب')->searchable(),
                Tables\Columns\TextColumn::make('status.status_name')->label('حالة الطلب')->searchable()->color(fn($record)=>$record->status->status_color),
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
