<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
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
    protected static ?string $navigationGroup = 'Produk';

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
                Textarea::make('description')->label('Deskripsi')->rows(3)->columnSpanFull(),
                Toggle::make('is_featured')->label('Produk Unggulan')->inline(),
                Toggle::make('is_active')->label('Aktif')->default(true)->inline(),
                TextInput::make('sort_order')->label('Urutan')->numeric()->default(0),
            ])->columns(2),

            Section::make('Foto Produk')
                ->description('Upload beberapa foto. Centang "Foto Utama" pada foto yang ingin ditampilkan pertama.')
                ->schema([
                    Repeater::make('images')
                        ->label('')
                        ->relationship()
                        ->schema([
                            FileUpload::make('image')
                                ->label('Upload Foto')
                                ->image()
                                ->disk('public')
                                ->directory('products')
                                ->columnSpan(2),
                            TextInput::make('image_url')
                                ->label('Atau URL Gambar')
                                ->url()
                                ->placeholder('https://...')
                                ->columnSpan(2),
                            TextInput::make('sort_order')
                                ->label('Urutan')
                                ->numeric()
                                ->default(0),
                            Toggle::make('is_primary')
                                ->label('Foto Utama')
                                ->inline(),
                        ])
                        ->columns(4)
                        ->addActionLabel('+ Tambah Foto')
                        ->collapsible(),
                ]),

            Section::make('Opsi Variasi')
                ->description('Tentukan jenis variasi, misal: Ukuran, Warna, Hook / Cantolan')
                ->schema([
                    Repeater::make('variantOptions')
                        ->label('')
                        ->relationship()
                        ->schema([
                            TextInput::make('name')
                                ->label('Nama Opsi')
                                ->required()
                                ->placeholder('Ukuran / Warna / Hook')
                                ->columnSpan(2),
                            TextInput::make('sort_order')
                                ->label('Urutan')
                                ->numeric()
                                ->default(0),
                            Repeater::make('values')
                                ->label('Nilai Pilihan')
                                ->relationship()
                                ->schema([
                                    TextInput::make('value')
                                        ->label('Nilai')
                                        ->required()
                                        ->placeholder('Dewasa / Natural / Hook Gold 10cm')
                                        ->columnSpan(2),
                                    FileUpload::make('image')
                                        ->label('Gambar Opsi')
                                        ->image()
                                        ->disk('public')
                                        ->directory('variant-values')
                                        ->imagePreviewHeight('60')
                                        ->columnSpan(2),
                                    TextInput::make('sort_order')
                                        ->label('Urutan')
                                        ->numeric()
                                        ->default(0),
                                ])
                                ->columns(5)
                                ->addActionLabel('+ Tambah Nilai')
                                ->columnSpanFull(),
                        ])
                        ->columns(3)
                        ->addActionLabel('+ Tambah Opsi Variasi')
                        ->collapsible(),
                ]),

            Section::make('Kombinasi Variasi & Harga')
                ->description('Klik "🔀 Generate Kombinasi" untuk buat otomatis dari opsi variasi di atas, lalu atur harga per tier.')
                ->headerActions([
                    FormAction::make('generateKombinasi')
                        ->label('🔀 Generate Kombinasi')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Generate Kombinasi Variasi')
                        ->modalDescription('Semua kombinasi dibuat otomatis dari opsi variasi. Yang sudah ada tidak akan ditimpa.')
                        ->modalSubmitActionLabel('Ya, Generate!')
                        ->action(function ($livewire) {
                            $product = $livewire->record;
                            $product->load('variantOptions.values');

                            $options = $product->variantOptions;

                            if ($options->isEmpty()) {
                                Notification::make()
                                    ->title('Belum ada opsi variasi!')
                                    ->warning()
                                    ->send();
                                return;
                            }

                            foreach ($options as $opt) {
                                if ($opt->values->isEmpty()) {
                                    Notification::make()
                                        ->title("Opsi \"{$opt->name}\" belum punya nilai!")
                                        ->warning()
                                        ->send();
                                    return;
                                }
                            }

                            // Cartesian product
                            $combinations = [[]];
                            foreach ($options as $opt) {
                                $newCombinations = [];
                                foreach ($combinations as $existing) {
                                    foreach ($opt->values as $val) {
                                        $newCombinations[] = array_merge($existing, [
                                            $opt->name => $val->value,
                                        ]);
                                    }
                                }
                                $combinations = $newCombinations;
                            }

                            $existingVariants = ProductVariant::where('product_id', $product->id)->get();
                            $created = 0;
                            $skipped = 0;

                            foreach ($combinations as $combination) {
                                if ($existingVariants->contains(fn($v) => $v->combination == $combination)) {
                                    $skipped++;
                                    continue;
                                }

                                ProductVariant::create([
                                    'product_id'  => $product->id,
                                    'combination' => $combination,
                                    'is_active'   => true,
                                    'sort_order'  => 0,
                                ]);
                                $created++;
                            }

                            Notification::make()
                                ->title("✅ {$created} kombinasi dibuat" . ($skipped > 0 ? ", {$skipped} dilewati" : ''))
                                ->success()
                                ->send();

                            $livewire->redirect(request()->header('Referer'));
                        }),
                ])
                ->schema([
                    Repeater::make('variants')
                        ->label('')
                        ->relationship()
                        ->schema([
                            TextInput::make('sku')
                                ->label('SKU (Opsional)')
                                ->placeholder('ZH-001'),
                            Toggle::make('is_active')->label('Aktif')->default(true)->inline(),
                            TextInput::make('sort_order')->label('Urutan')->numeric()->default(0),
                            KeyValue::make('combination')
                                ->label('Kombinasi Variasi')
                                ->keyLabel('Nama Opsi (cth: Ukuran)')
                                ->valueLabel('Nilai (cth: Dewasa)')
                                ->addActionLabel('+ Tambah')
                                ->columnSpanFull(),
                            Repeater::make('priceTiers')
                                ->label('Harga Grosir per Tier')
                                ->relationship()
                                ->schema([
                                    TextInput::make('min_qty')->label('Min Qty')->numeric()->required()->placeholder('1'),
                                    TextInput::make('max_qty')->label('Max Qty')->numeric()->nullable()->placeholder('99'),
                                    TextInput::make('price')->label('Harga/pcs')->numeric()->required()->prefix('Rp'),
                                ])
                                ->columns(3)
                                ->defaultItems(4)
                                ->addActionLabel('+ Tambah Tier')
                                ->columnSpanFull(),
                        ])
                        ->columns(3)
                        ->addActionLabel('+ Tambah Kombinasi')
                        ->collapsible(),
                ]),

            Section::make('Harga Default')
                ->description('Isi jika produk tidak punya variasi, atau sebagai harga fallback.')
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
                ])
                ->collapsible()
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Foto')
                    ->size(60)
                    ->disk('public')
                    ->defaultImageUrl(fn ($record) => $record->image_url),
                TextColumn::make('name')->label('Nama')->searchable()->limit(35),
                TextColumn::make('category.name')->label('Kategori')->badge(),
                TextColumn::make('variants_count')
                    ->label('Variasi')
                    ->counts('variants')
                    ->badge(),
                TextColumn::make('images_count')
                    ->label('Foto')
                    ->counts('images')
                    ->badge(),
                IconColumn::make('is_featured')->label('Unggulan')->boolean(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->filters([
                SelectFilter::make('category_id')->label('Kategori')
                    ->options(Category::active()->pluck('name', 'id')),
                TernaryFilter::make('is_active')->label('Status Aktif'),
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
