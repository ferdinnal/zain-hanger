<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Services\OrderService;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model           = Order::class;
    protected static ?string $navigationIcon  = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Pesanan Masuk';
    protected static ?int    $navigationSort  = 1;

    public static function getNavigationGroup(): string
    {
        return 'Pesanan';
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Order::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            \Filament\Forms\Components\TextInput::make('customer_name')->label('Nama')->disabled(),
            \Filament\Forms\Components\TextInput::make('customer_phone')->label('No. HP')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_code')
                    ->label('Kode Order')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->weight('bold'),
                TextColumn::make('customer_name')
                    ->label('Pelanggan')
                    ->searchable(),
                TextColumn::make('customer_phone')
                    ->label('No. HP')
                    ->copyable(),
                TextColumn::make('items_count')
                    ->label('Item')
                    ->counts('items')
                    ->badge(),
                TextColumn::make('total_amount')
                    ->label('Total')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending'    => 'warning',
                        'confirmed'  => 'primary',
                        'processing' => 'info',
                        'shipped'    => 'info',
                        'done'       => 'success',
                        'cancelled'  => 'danger',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => Order::STATUS_LABELS[$state]['label'] ?? $state),
                TextColumn::make('source')
                    ->label('Sumber')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === 'cart' ? 'Keranjang' : 'Langsung'),
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options([
                    'pending'    => 'Menunggu',
                    'confirmed'  => 'Dikonfirmasi',
                    'processing' => 'Diproses',
                    'shipped'    => 'Dikirim',
                    'done'       => 'Selesai',
                    'cancelled'  => 'Dibatalkan',
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('change_status')
                    ->label('Update Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->form([
                        Select::make('status')
                            ->label('Status Baru')
                            ->options([
                                'confirmed'  => 'Dikonfirmasi',
                                'processing' => 'Diproses',
                                'shipped'    => 'Dikirim',
                                'done'       => 'Selesai',
                                'cancelled'  => 'Dibatalkan',
                            ])
                            ->required(),
                    ])
                    ->action(fn (Order $record, array $data) =>
                        app(OrderService::class)->updateStatus($record, $data['status'])
                    ),
                Tables\Actions\Action::make('open_wa')
    ->label('Buka WA')
    ->icon('heroicon-o-chat-bubble-left-right')
    ->color('success')
    ->url(function (Order $record) {
        $phone = preg_replace('/[^0-9]/', '', $record->customer_phone);
        // Pastikan format internasional
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $firstItem    = $record->items->first();
        $snapshot     = $firstItem?->product_snapshot ?? [];
        $variantLabel = $snapshot['variant_label'] ?? null;

        $message =
            "Halo *{$record->customer_name}*, terima kasih sudah memesan di " .
            Setting::get('site_name', 'Zain Hanger') . "! 🙏\n\n" .
            "Berikut detail pesanan Anda:\n" .
            "🔖 Kode Order: *{$record->order_code}*\n" .
            "📦 Produk: *{$snapshot['name']}*\n" .
            ($variantLabel ? "Variasi: {$variantLabel}\n" : '') .
            "Qty: {$firstItem?->qty} pcs\n" .
            "Total: *{$record->total_formatted}*\n\n" .
            "Alamat pengiriman:\n{$record->shipping_address}\n\n" .
            "Pesanan Anda sedang kami proses. Kami akan segera menghubungi Anda kembali. 😊";

        return 'https://wa.me/' . $phone . '?text=' . urlencode($message);
    })
    ->openUrlInNewTab(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view'  => Pages\ViewOrder::route('/{record}'),
        ];
    }
}
