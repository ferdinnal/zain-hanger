@extends('layouts.app')
@php $pageTitle = $product->name @endphp

@section('content')
<section class="section-padding" style="padding-top: 120px;">
    <div class="container mx-auto px-5">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm mb-10" style="color: var(--text-muted)">
            <a href="{{ route('home') }}" class="hover:underline" style="color: var(--primary)">Home</a>
            <span>/</span>
            <a href="{{ route('catalog.index') }}" class="hover:underline" style="color: var(--primary)">Katalog</a>
            <span>/</span>
            <span>{{ $product->name }}</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-start">

            {{-- Gambar Produk --}}
            <div>
                <div style="border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-lg); height: 450px;">
                    <img src="{{ $product->image_url }}"
                         alt="{{ $product->name }}"
                         class="w-full h-full object-cover">
                </div>
            </div>

            {{-- Info Produk --}}
            <div x-data="productDetail({{ $product->priceTiers->toJson() }})">
                {{-- Badge --}}
                <div class="flex gap-2 mb-4 flex-wrap">
                    <span class="text-xs font-bold px-3 py-1 rounded-full" style="background: var(--secondary); color: var(--primary)">
                        {{ $product->category->name ?? '' }}
                    </span>
                    @if($product->is_anti_theft)
                        <span class="text-xs font-bold px-3 py-1 rounded-full" style="background: #ef4444; color: white">
                            ANTI THEFT
                        </span>
                    @endif
                </div>

                <h1 class="text-3xl font-bold mb-2" style="color: var(--primary)">{{ $product->name }}</h1>

                {{-- Variasi --}}
                <div class="flex gap-3 flex-wrap mb-6">
                    @if($product->kepala_label)
                        <span class="text-sm px-3 py-1 rounded-full border" style="border-color: var(--secondary); color: var(--text-muted)">
                            {{ $product->kepala_label }}
                        </span>
                    @endif
                    @if($product->jenis_label)
                        <span class="text-sm px-3 py-1 rounded-full border" style="border-color: var(--secondary); color: var(--text-muted)">
                            {{ $product->jenis_label }}
                        </span>
                    @endif
                </div>

                @if($product->description)
                    <p class="mb-6" style="color: var(--text-muted)">{{ $product->description }}</p>
                @endif

                {{-- Harga Tier --}}
                <div class="mb-6 p-5 rounded-xl" style="background: var(--bg-color); border: 1px solid #e5e7eb;">
                    <h3 class="text-sm font-bold mb-3" style="color: var(--text-muted)">TABEL HARGA</h3>
                    <div class="space-y-2">
                        @foreach($product->priceTiers as $tier)
                        <div class="flex justify-between items-center text-sm py-1 border-b border-gray-100">
                            <span style="color: var(--text-muted)">
                                {{ number_format($tier->min_qty, 0, ',', '.') }}
                                {{ $tier->max_qty ? '– ' . number_format($tier->max_qty, 0, ',', '.') : '+' }} pcs
                            </span>
                            <span class="font-bold" style="color: var(--primary)">
                                Rp {{ number_format($tier->price, 0, ',', '.') }}/pcs
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Input Qty --}}
                <div class="mb-6">
                    <label class="block text-xs font-bold mb-2" style="color: var(--text-muted)">JUMLAH ORDER (PCS)</label>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-3 border rounded-lg px-4 py-2" style="border-color: #e5e7eb;">
                            <button @click="decreaseQty()" class="text-xl font-bold w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors" style="color: var(--primary)">−</button>
                            <input type="number" x-model="qty" @input="calcPrice()" min="1"
                                   class="w-20 text-center text-lg font-semibold border-none outline-none"
                                   style="color: var(--primary)">
                            <button @click="increaseQty()" class="text-xl font-bold w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors" style="color: var(--primary)">+</button>
                        </div>
                        <span class="text-sm" style="color: var(--text-muted)">pcs</span>
                    </div>
                </div>

                {{-- Kalkulasi Harga --}}
                <div class="mb-8 p-5 rounded-xl" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-white/80 text-sm">Harga per pcs</span>
                        <span class="text-white font-semibold" x-text="formatRupiah(pricePerUnit)"></span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-white/80 text-sm">Qty</span>
                        <span class="text-white font-semibold" x-text="qty + ' pcs'"></span>
                    </div>
                    <div class="border-t border-white/20 pt-3 mt-3 flex justify-between items-center">
                        <span class="text-white font-bold">Total</span>
                        <span class="text-2xl font-bold" style="color: var(--secondary)" x-text="formatRupiah(totalPrice)"></span>
                    </div>
                    <div class="mt-2">
                        <span class="text-xs text-white/60" x-text="tierLabel"></span>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex flex-col gap-3">
                    <button @click="orderViaWA()" class="btn-wa text-base py-4" style="border-radius: var(--radius-md);">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Pesan via WhatsApp
                    </button>

                    <button @click="addToCart()" class="btn text-base py-4 w-full justify-center"
                            style="border: 2px solid var(--primary); color: var(--primary); border-radius: var(--radius-md);"
                            onmouseover="this.style.background='var(--primary)';this.style.color='white'"
                            onmouseout="this.style.background='transparent';this.style.color='var(--primary)'">
                        🛒 Tambah ke Keranjang
                    </button>
                </div>

            </div>
        </div>

        {{-- Produk Terkait --}}
        @if($related->count() > 0)
        <div class="mt-24">
            <div class="section-header">
                <h2 class="section-title">Produk Terkait</h2>
                <p class="section-subtitle">Produk lain yang mungkin kamu suka</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($related as $rel)
                    @include('public.catalog._product-card', ['product' => $rel])
                @endforeach
            </div>
        </div>
        @endif

    </div>
</section>

@push('scripts')
<script>
function productDetail(priceTiers) {
    return {
        priceTiers,
        qty: 1,
        pricePerUnit: 0,
        totalPrice: 0,
        tierLabel: '',

        init() { this.calcPrice(); },

        calcPrice() {
            const qty = parseInt(this.qty) || 1;
            const sorted = [...this.priceTiers].sort((a, b) => a.min_qty - b.min_qty);
            let tier = sorted[0];
            for (const t of sorted) {
                if (qty >= t.min_qty && (t.max_qty === null || qty <= t.max_qty)) tier = t;
            }
            this.pricePerUnit = tier ? tier.price : 0;
            this.totalPrice   = this.pricePerUnit * qty;
            this.tierLabel    = tier
                ? `Tier: ${tier.min_qty.toLocaleString('id')}${tier.max_qty ? '–' + tier.max_qty.toLocaleString('id') : '+'} pcs`
                : '';
        },

        increaseQty() { this.qty = parseInt(this.qty) + 1; this.calcPrice(); },
        decreaseQty() { if (parseInt(this.qty) > 1) { this.qty = parseInt(this.qty) - 1; this.calcPrice(); } },

        formatRupiah(val) {
            return 'Rp ' + parseInt(val).toLocaleString('id-ID');
        },

        orderViaWA() {
            const waNumber = '{{ preg_replace('/[^0-9]/', '', setting('contact_whatsapp', '6282291409209')) }}';
            const message = `Halo {{ setting('site_name', 'Zain Hanger') }}, saya ingin memesan:\n\n📦 *{{ $product->name }}*\n{{ $product->kepala_label ? 'Kepala: ' . $product->kepala_label . '\n' : '' }}{{ $product->jenis_label ? 'Jenis: ' . $product->jenis_label . '\n' : '' }}Qty: ${this.qty} pcs\nHarga: ${this.formatRupiah(this.pricePerUnit)}/pcs\nTotal: ${this.formatRupiah(this.totalPrice)}\n\nMohon konfirmasinya 🙏`;
            window.open(`https://wa.me/${waNumber}?text=${encodeURIComponent(message)}`, '_blank');
        },

        addToCart() {
            @auth
            fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ product_id: {{ $product->id }}, qty: this.qty })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const badge = document.querySelector('.cart-badge');
                    if (badge) badge.textContent = data.cart_count;
                    alert('Produk berhasil ditambahkan ke keranjang!');
                }
            });
            @else
            window.location.href = '{{ route('login') }}';
            @endauth
        }
    }
}
</script>
@endpush
@endsection
