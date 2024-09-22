<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers\SkusRelationManager;
use App\Models\AttributeOption;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\GlobalSearch\Actions\Action;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


class ProductResource extends Resource
{

    protected static ?string $model = Product::class;
    protected static ?string $modelLabel = 'منتج';
    protected static ?string $pluralLabel = 'المنتجات';
    protected static ?string $navigationGroup = 'تفاصيل المنتجات';
    protected static ?int $navigationSort = -20;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'المنتجات';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('معلومات المنتج')
                    ->schema([


                        Forms\Components\TextInput::make('name')->label('اسم المنتج')->placeholder('ادخل  اسم المنتج')->required()->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\Select::make('category_id')
                            ->label('القسم الرئيسي')
                            ->options(Category::all()->pluck('name', 'id')->toArray())
                            ->reactive()
                            ->searchable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->afterStateUpdated(fn (callable $set) => $set('sub_category_id', null)),
                        Forms\Components\Select::make('sub_category_id')
                            ->label('القسم الفرعي')
                            ->options(function (callable $get) {
                                $category = Category::find($get('category_id'));
                                if (!$category) {
                                    return SubCategory::all()->pluck('name', 'id')->toArray();
                                }
                                return $category->Subcategoreies->pluck('name', 'id')->toArray();
                            })
                            ->required()
                            ->preload()
                            ->searchable()
                        ,


                        Textarea::make('description')
                            ->label(' وصف المنتج')
                            ->placeholder('ادخل وصف المنتج')
                            ->autosize()
                            ->required()
                            ->string(),

                    ])->columns(2),
                Section::make('صور المنتج')
                    ->schema([

                        SpatieMediaLibraryFileUpload::make('photo')->label('الصورة')
                            ->multiple()
                            ->disk('public')
                            ->image()
                            ->conversion('thumb')
                            ->downloadable()
                            ->minFiles(1)
                            ->maxFiles(8)
                            ->maxSize(5120)
                            ->reorderable(),

                    ]),

                Section::make('الحالة')
                    ->schema([
                        Forms\Components\Toggle::make('status')->label('الحالة')->hiddenLabel()
                            ->onColor('success')->default('success')
                            ->offColor('danger'),


                    ]),
            ]);
    }
    public static function  infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Section::make('معلومات المنتج')->schema([
                    TextEntry::make('name')->label('اسم المنتج ')->color('info'),
                    TextEntry::make('subcategory.category.name')->label('  قسم المنتج الرئيسي')->color('danger'),
                    TextEntry::make('subcategory.name')->label(' قسم المنتج الفرعي')->color('info'),
                    TextEntry::make('quantity')->label('اجمالي الكميات')->numeric()->color('danger')->getStateUsing(fn ($record) => $record->total_quantity),
                    Fieldset::make('وصف المنتج')->schema([
                        TextEntry::make('description')->label('  وصف المنتج')->color('danger')->hiddenLabel(),
                    ])->columnSpan(1)->columns(1)

                ])->columns(4),

                \Filament\Infolists\Components\Section::make('صور المنتج')->schema([
                    ImageEntry::make('media')
                        ->label('Media')->hiddenLabel()
                        ->getStateUsing(function ($record) {
                            return $record->getMedia()->map(fn($media) => $media->getUrl('thumb'))->toArray();
                        })->square()

                ])->columns(2),


                \Filament\Infolists\Components\Section::make(' معلومات المنتج الفرعية')->schema([
                    Grid::make()->schema([
                    RepeatableEntry::make('skus')->label('خصائص المنتج الفرعية')
                        ->schema([
                            TextEntry::make('price')->label('سعر المنتج')->color('danger')->money('YER')->columnSpan(2),
                            TextEntry::make('quantity')->label('كمية المنتج')->color('danger')->numeric()->columnSpan(2),
                            TextEntry::make('discount.value')->label('قيمة الخصم')->color('danger')->numeric()->hidden(fn ($record) => empty($record->discount->value))->suffix('%')->columnSpan(2),
                            TextEntry::make('discount.discounted_price')->label('السعر بعد الخصم')->money('YER')->color('danger')->hidden(fn ($record) => empty($record->discount->discounted_price))->columnSpan(2),
                            TextEntry::make('code')->label('كود المنتج')->color('danger')->columnSpan(2),

                            RepeatableEntry::make('attribute_Options')->label('الصفات الفرعية')->schema([
                                TextEntry::make('value')->color('info')->label(function (AttributeOption $record) {
                                    $attribute = $record->attribute->name;
                                    return 'يمتلك '. $attribute;}),
                                ColorEntry::make('values.value')->hiddenLabel()->hidden(fn ($record) => empty($record->values->value)),
                            ])->contained()->grid(5)->columnSpan(5)->hiddenLabel(),
                        ])->columns(6)->columnSpan(5),
                        ])
                    ])
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('name')->label('الاسم')->searchable(),
                Tables\Columns\ToggleColumn::make('status')->label('الحالة')->onColor('success')->offColor('danger'),
                Tables\Columns\IconColumn::make('availability')->label('التوفر')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->before(function ($record, $action) {
                    // Check if the product has related orders
                    if ($record->skus()->exists()||$record->favorite()->exists()) {
                        // Prevent deletion and show a notification
                        Notification::make()
                            ->title('فشل الحذف')
                            ->body('لايمكنك الحذف  لانها مرتبطة بطلبات ')
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
//            SkusRelationManager::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),

        ];
    }
    public static function getGloballySearchableAttributes(): array
    {
        return ['name','skus.code'];
    }
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return ['اسم المنتج'=>$record->name];
    }
    public static function getGlobalSearchEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['skus']);
    }
    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return ProductResource::getUrl('view', ['record' => $record]);
    }
    public static function getGlobalSearchResultActions(Model $record): array
    {
        return [
            Action::make('edit')
                ->iconButton()
                ->icon('heroicon-s-pencil')
                ->url(static::getUrl('edit', ['record' => $record])),
        ];
    }




}
