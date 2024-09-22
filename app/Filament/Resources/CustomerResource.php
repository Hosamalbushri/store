<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\AddressesRelationManager;
use App\Filament\Resources\OrderResource\RelationManagers\OrdersRelationManager;
use App\Models\Customer;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\GlobalSearch\Actions\Action;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Customer::class;
//    protected static string | array $routeMiddleware = ['auth:api'];

    protected static ?string $modelLabel = 'عميل';
    protected static ?string $pluralLabel = 'العملاء';
    protected static ?string $navigationGroup = 'تفاصيل العملاء';
    protected static ?int $navigationSort = -25;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'العملاء';
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
                Forms\Components\TextInput::make('name')
                    ->disabled()
                    ->label('اسم العميل')
                    ->maxLength(255),


            ]);
    }
    public static function  infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                \Filament\Infolists\Components\Section::make('معلومات العميل')->schema([
                    TextEntry::make('name')->label('اسم العميل ')->color('info'),
                    TextEntry::make('email')->label('البريد الالكتروني')->color('danger'),
                    TextEntry::make('phone')->label('رقم الهاتف')->color('info')->hidden(fn ($record) => empty($record->phone)),


                ])->columns(3),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('اسم العميل')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')->label('البريد الالكتروني')
                    ->searchable(),
                ImageColumn::make('media')
                    ->label('الصورة الشخصية')
                    ->getStateUsing(fn (Customer $record)=>  $record->getFirstMediaUrl('images'))
                    ->defaultImageUrl(fn ($record): string => 'https://ui-avatars.com/api/?name='.urlencode($record->name).'&color=fff&background=#000000')
                    ->circular(),
                Tables\Columns\ToggleColumn::make('status')->label('الحالة')->onColor('success')->offColor('danger'),
                Tables\Columns\TextColumn::make('created_at')->label('تاريخ الانشاء')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->label('تاريخ التعديل')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),




            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AddressesRelationManager::class,
            OrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
            'view'=>pages\ViewCustomer::route('/{record}'),
        ];
    }
    public static function canCreate(): bool
    {
        return false;
    }
    public static function canDelete(Model $record): bool
    {
        if ($record->orders()->count() == 0)
        {
            return true;
        }
        if ($record->order_detaile()->count() == 0)
        {
            return true;

        }
        return false;
    }
    public static function getGloballySearchableAttributes(): array
    {
        return ['name','email','phone','orders.order_number','orders.order_date','addresses.phone'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'اسم العميل'=>$record->name,
            'البريد الالكتروني'=>$record->email,
        ];
    }
    public static function getGlobalSearchEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['orders','addresses']);
    }
    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return CustomerResource::getUrl('edit', ['record' => $record]);
    }

}
