<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model           = Product::class;
    protected static ?string $navigationIcon  = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Produk';

    public static function getNavigationGroup(): string
    {
        return 'Produk';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Produk')->schema([
                Select::make('category_id')
                    ->label('Kategori')
                    ->options(Category::active()->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                TextInput::make('name')
                    ->label('Nama Produk')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) =>
                        $set('slug', Str::slug($state ?? ''))
                    ),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Select::make('jenis')
                    ->label('Jenis Badan')
                    ->options(Product::JENIS_LABELS)
                    ->nullable(),
                Select::make('kepala')
                    ->label('Jenis Kepala')
                    ->options(Product::KEPALA_LABELS)
                    ->nullable(),
                Toggle::make('is_anti_theft')->label('Anti Theft')->inline(),
                Toggle::make('is_featured')->label('Produk Unggulan')->inline(),
                Toggle::make('is_active')->label('Aktif')->default(true)->inline(),
                Textarea::make('description')->label('Deskripsi')->rows(3)->columnSpanFull(),

                // ── FIX UTAMA: tambah disk('public') supaya file tersimpan
                //    di storage/app/public/products/ dan bisa diakses via URL
                FileUpload::make('image')
                    ->label('Upload Foto (kosongkan jika tidak ingin ganti)')
                    ->image()
                    ->disk('public')
                    ->directory('products')
                    ->deletable(false)
                    ->imageEditor()
                    ->afterStateHydrated(function (FileUpload $component, $state) {
                        // Paksa kosong — hindari bug "Waiting for size" di Windows
                        $component->state(null);
                    })
                    ->afterStateUpdated(function (Set $set, $state) {
                        if (! $state) return;
                        $filename = is_array($state) ? array_key_first($state) : $state;
                        if ($filename) {
                            $set('image_url', '/storage/products/' . $filename);
                        }
                    })
                    ->helperText(fn ($record) => $record?->image
                        ? new \Illuminate\Support\HtmlString(
                            '<img src="' . asset('storage/' . $record->image) . '" style="max-height:120px;margin-top:8px;border-radius:6px">'
                        )
                        : 'Belum ada foto'
                    ),

                TextInput::make('image_url')
                    ->label('Atau URL Gambar Eksternal')
                    ->url()
                    ->placeholder('https://...'),

                TextInput::make('sort_order')->label('Urutan')->numeric()->default(0),
            ])->columns(2),

            Section::make('Harga per Tier Qty')
                ->description('4 tier sesuai katalog: 1-99, 100-999, 1000-9999, 10000+')
                ->schema([
                    Repeater::make('priceTiers')
                        ->label('')
                        ->relationship()
                        ->schema([
                            TextInput::make('min_qty')->label('Min Qty')->numeric()->required(),
                            TextInput::make('max_qty')->label('Max Qty')->numeric()->nullable()->placeholder('Kosong = tak terbatas'),
                            TextInput::make('price')->label('Harga/pcs')->numeric()->required()->prefix('Rp'),
                        ])
                        ->columns(3)
                        ->defaultItems(4)
                        ->addActionLabel('+ Tambah Tier')
                        ->reorderable(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tidak diubah — sudah berjalan dengan baik
                ImageColumn::make('image_url')
                    ->label('Foto')
                    ->size(60)
                    ->defaultImageUrl(fn ($record) => $record->image_url),
                TextColumn::make('name')->label('Nama')->searchable()->limit(35),
                TextColumn::make('category.name')->label('Kategori')->badge(),
                TextColumn::make('jenis_label')->label('Jenis'),
                TextColumn::make('kepala_label')->label('Kepala')->limit(20),
                IconColumn::make('is_anti_theft')->label('Anti Theft')->boolean(),
                IconColumn::make('is_featured')->label('Unggulan')->boolean(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('min_price')
                    ->label('Harga Mulai')
                    ->getStateUsing(fn ($record) =>
                        'Rp ' . number_format($record->priceTiers->min('price') ?? 0, 0, ',', '.')
                    ),
            ])
            ->filters([
                SelectFilter::make('category_id')->label('Kategori')
                    ->options(Category::active()->pluck('name', 'id')),
                TernaryFilter::make('is_active')->label('Status Aktif'),
                TernaryFilter::make('is_anti_theft')->label('Anti Theft'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
