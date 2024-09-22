<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Filament\Resources\CategoryResource\RelationManagers\SubcategoreiesRelationManager;
use App\Models\Category;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $modelLabel = 'قسم';
    protected static ?string $pluralLabel = 'الاقسام';


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'الاقسام الرئيسية';

    protected static ?string $navigationLabel = 'الاقسام';
    protected static ?int $navigationSort = -20;
    public static function form(Form $form): Form
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
                            ->image()
                            ->disk('public')
                            ->image()
                            ->conversion('thumb')
                            ->moveFiles('product_images')
                            ->downloadable()
                            ->storeFileNamesIn('product_images'),

                    ]),
                Fieldset::make('')->schema([
                    Forms\Components\Toggle::make('status')->label('الحالة')
                        ->onColor('success')->default('success')
                        ->offColor('danger'),

                ]),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('الاسم')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('status')->label('الحالة')->onColor('success')->offColor('danger'),
                ImageColumn::make('media')
                    ->label('صورة القسم')
                    ->getStateUsing(fn (Category $record)=>  $record->getMedia()->map(fn($media) => $media->getUrl('thumb'))->toArray())
                    ->defaultImageUrl(fn ($record): string => 'https://ui-avatars.com/api/?name='.urlencode($record->name).'&color=fff&background=#000000')
                    ->circular(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->before(function ($record, $action) {
                    // Check if the product has related orders
                    if ($record->Subcategoreies()->exists()) {
                        // Prevent deletion and show a notification
                        Notification::make()
                            ->title('فشل الحذف')
                            ->body('لايمكنك حذف هذه القسم لان لدية اقسام فرعية')
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
        if ($record->Subcategoreies()->exists()) {
            // Prevent deletion and show a notification
            Notification::make()
                ->title('فشل الحذف')
                ->body('لايمكنك حذف هذه القسم لان لدية اقسام فرعية')
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
            SubcategoreiesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
    public static function getGloballySearchableAttributes(): array
    {
        return ['name','Subcategoreies.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'اسم القسم'=>$record->name,
        ];
    }
    public static function getGlobalSearchEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['Subcategoreies']);
    }
    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return CategoryResource::getUrl('edit', ['record' => $record]);
    }
}
