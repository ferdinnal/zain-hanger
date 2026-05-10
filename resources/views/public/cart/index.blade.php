@extends('layouts.app')
@php $pageTitle = 'Keranjang Belanja' @endphp

@section('content')
<section class="section-padding" style="padding-top: 120px;">
    <div class="container mx-auto px-5">
        <div class="section-header">
            <h2 class="section-title">Keranjang Belanja</h2>
            <p class="section-subtitle">Review produk sebelum melakukan pemesanan</p>
        </div>

        @if($cartItems->isEmpty())
            <div class="text-center py-24">
                <div class="text-8xl mb-6">🛒</div>
                <h3 class="text-2xl font-semibold mb-3" style="color: var(--primary)">Keranjang masih kosong</h3>
                <p class="mb-8" style="color: var(--text-muted)">Tambahkan produk favoritmu ke keranjang terlebih dahulu</p>
                <a href="{{ route('catalog.index') }}" class="btn btn-primary">Mulai Belanja</a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

                {{-- Cart Items --}}
                <div class="lg:col-span-2 space-y-4">
                    @foreach($cartItems as $item)
                    <div class="glass rounded-xl p-5 flex items-start gap-5" id="cart-item-{{ $item->id }}">
                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}"
                             class="w-24 h-24 object-cover rounded-lg flex-shrink-0">
                        <div class="flex-1">
                            <h4 class="font-semibold text-base mb-1">{{ $item->product->name }}</h4>
                            <div class="flex gap-2 flex-wrap mb-3">
                                @if($item->product->kepala_label)
                                    <span class="text-xs px-2 py-1 rounded bg-warm-100" style="color: var(--text-muted)">{{ $item->product->kepala_label }}</span>
                                @endif
                                @if($item->product->jenis_label)
                                    <span class="text-xs px-2 py-1 rounded bg-warm-100" style="color: var(--text-muted)">{{ $item->product->jenis_label }}</span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between">
                                <div x-data="cartItemPrice({{ $item->qty }}, {{ $item->product->priceTiers->toJson() }})">
                                    <div class="flex items-center gap-3 mb-1">
                                        <button @click="decreaseQty({{ $item->id }})" class="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center text-gray-600 hover:border-primary hover:text-primary transition-colors">−</button>
                                        <span x-text="qty" class="text-base font-semibold w-8 text-center"></span>
                                        <button @click="increaseQty({{ $item->id }})" class="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center text-gray-600 hover:border-primary hover:text-primary transition-colors">+</button>
                                        <span class="text-sm" style="color: var(--text-muted)">pcs</span>
                                    </div>
                                    <div>
                                        <span class="font-bold" style="color: var(--primary)" x-text="formatRupiah(pricePerUnit)"></span>
                                        <span class="text-xs ml-1" style="color: var(--text-muted)">/pcs</span>
                                    </div>
                                    <div class="text-sm font-bold" style="color: var(--secondary)">
                                        Total: <span x-text="formatRupiah(total)"></span>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-600 transition-colors text-sm">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Order Summary --}}
                <div class="glass rounded-xl p-6 h-fit sticky top-24">
                    <h3 class="text-lg font-bold mb-6 pb-3 border-b" style="color: var(--primary); border-color: var(--secondary)">
                        Ringkasan Pesanan
                    </h3>

                    <div class="space-y-3 mb-6">
                        @foreach($cartItems as $item)
                        <div class="flex justify-between text-sm">
                            <span style="color: var(--text-muted)">{{ $item->product->name }} ({{ $item->qty }}x)</span>
                            <span class="font-semibold">{{ $item->subtotal_formatted }}</span>
                        </div>
                        @endforeach
                    </div>

                    <div class="border-t pt-4 mb-6">
                        <div class="flex justify-between font-bold text-base">
                            <span>Total</span>
                            <span style="color: var(--primary)">{{ $totalFormatted }}</span>
                        </div>
                        <p class="text-xs mt-2" style="color: var(--text-muted)">*Belum termasuk ongkir, akan dikonfirmasi admin</p>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="btn btn-primary w-full justify-center mb-3">
                        Lanjut Checkout
                    </a>

                    <a href="{{ route('catalog.index') }}" class="block text-center text-sm" style="color: var(--primary)">
                        ← Lanjut Belanja
                    </a>
                </div>

            </div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
function cartItemPrice(initialQty, priceTiers) {
    return {
        qty: initialQty,
        priceTiers,
        pricePerUnit: 0,
        total: 0,
        init() { this.calc(); },
        calc() {
            const sorted = [...this.priceTiers].sort((a,b) => a.min_qty - b.min_qty);
            let tier = sorted[0];
            for (const t of sorted) {
                if (this.qty >= t.min_qty && (t.max_qty === null || this.qty <= t.max_qty)) tier = t;
            }
            this.pricePerUnit = tier.price;
            this.total = tier.price * this.qty;
        },
        increaseQty(itemId) {
            this.qty++;
            this.calc();
            this.updateServer(itemId);
        },
        decreaseQty(itemId) {
            if (this.qty > 1) { this.qty--; this.calc(); this.updateServer(itemId); }
        },
        updateServer(itemId) {
            fetch(`/cart/${itemId}`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ qty: this.qty })
            });
        },
        formatRupiah(v) { return 'Rp ' + parseInt(v).toLocaleString('id-ID'); }
    }
}
</script>
@endpush
