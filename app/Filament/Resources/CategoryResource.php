<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model           = Category::class;
    protected static ?string $navigationIcon  = 'heroicon-o-squares-2x2';
    protected static ?string $navigationLabel = 'Kategori';

    public static function getNavigationGroup(): string
    {
        return 'Produk';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Nama Kategori')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Set $set, ?string $state) =>
                    $set('slug', Str::slug($state ?? ''))
                ),
            TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true),
            Textarea::make('description')
                ->label('Deskripsi')
                ->rows(2),
            FileUpload::make('image')
                ->label('Foto Kategori')
                ->image()
                ->directory('categories')
                ->imageEditor(),
            TextInput::make('image_url')
                ->label('Atau URL Gambar Eksternal')
                ->url()
                ->placeholder('https://...'),
            TextInput::make('sort_order')
                ->label('Urutan Tampil')
                ->numeric()
                ->default(0),
            Toggle::make('is_active')
                ->label('Aktif')
                ->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Foto')
                    ->size(60)
                    ->defaultImageUrl('https://images.unsplash.com/photo-1591047139829-d91aecb6caea?q=80&w=100'),
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('slug')->label('Slug'),
                TextColumn::make('description')->label('Deskripsi')->limit(40),
                TextColumn::make('sort_order')->label('Urutan')->sortable(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit'   => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
