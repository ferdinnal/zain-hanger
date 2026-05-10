@extends('layouts.app')
@php $pageTitle = 'Riwayat Pesanan' @endphp

@section('content')
<section class="section-padding" style="padding-top: 120px;">
    <div class="container mx-auto px-5">
        <div class="section-header">
            <h2 class="section-title">Riwayat Pesanan</h2>
            <p class="section-subtitle">Semua pesanan yang pernah kamu buat</p>
        </div>

        @if($orders->isEmpty())
            <div class="text-center py-24">
                <div class="text-8xl mb-6">📋</div>
                <h3 class="text-2xl font-semibold mb-3" style="color: var(--primary)">Belum ada pesanan</h3>
                <p class="mb-8" style="color: var(--text-muted)">Yuk mulai belanja produk hanger kayu premium!</p>
                <a href="{{ route('catalog.index') }}" class="btn btn-primary">Lihat Katalog</a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($orders as $order)
                <div class="glass rounded-xl p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <p class="font-bold text-lg" style="color: var(--primary)">
                                #{{ $order->order_code }}
                            </p>
                            <p class="text-sm mt-1" style="color: var(--text-muted)">
                                {{ $order->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold"
                              style="background: var(--secondary); color: var(--primary)">
                            {{ $order->status_label }}
                        </span>
                    </div>

                    <div class="space-y-2 mb-4">
                        @foreach($order->items as $item)
                        <div class="flex justify-between text-sm">
                            <span style="color: var(--text-muted)">
                                {{ $item->product_snapshot['name'] ?? '-' }}
                                ({{ $item->qty }} pcs)
                            </span>
                            <span class="font-semibold">{{ $item->subtotal_formatted }}</span>
                        </div>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div>
                            <span class="text-sm" style="color: var(--text-muted)">Total: </span>
                            <span class="font-bold" style="color: var(--primary)">{{ $order->total_formatted }}</span>
                        </div>
                        <a href="{{ $order->getWaUrl() }}" target="_blank"
                           class="btn-wa inline-flex w-auto px-4 py-2 text-xs rounded">
                            💬 Chat Admin
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-8">{{ $orders->links() }}</div>
        @endif
    </div>
</section>
@endsection
