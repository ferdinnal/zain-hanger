<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Detail Pesanan')->schema([
                TextEntry::make('order_code')->label('Kode Order')->copyable(),
                TextEntry::make('customer_name')->label('Pelanggan'),
                TextEntry::make('customer_phone')->label('No. HP')->copyable(),
                TextEntry::make('customer_email')->label('Email'),
                TextEntry::make('shipping_address')->label('Alamat Kirim'),
                TextEntry::make('notes')->label('Catatan'),
                TextEntry::make('total_formatted')->label('Total'),
                TextEntry::make('status_label')->label('Status')
                    ->badge()
                    ->color(fn ($record) => match ($record->status) {
                        'pending'   => 'warning',
                        'confirmed' => 'primary',
                        'done'      => 'success',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),
                TextEntry::make('created_at')->label('Tanggal')->dateTime('d M Y H:i'),
            ])->columns(2),

            Section::make('Item Pesanan')->schema([
                RepeatableEntry::make('items')->schema([
                    TextEntry::make('product_snapshot.name')->label('Produk'),
                    TextEntry::make('product_snapshot.kepala_label')->label('Kepala'),
                    TextEntry::make('product_snapshot.jenis_label')->label('Jenis'),
                    TextEntry::make('qty')->label('Qty')->suffix(' pcs'),
                    TextEntry::make('price_per_unit')
                        ->label('Harga/pcs')
                        ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                    TextEntry::make('subtotal_formatted')->label('Subtotal'),
                ])->columns(3),
            ]),
        ]);
    }
}
