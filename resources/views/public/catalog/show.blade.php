@extends('layouts.app')
@php $pageTitle = $product->name @endphp

@section('content')
<meta name="auth-check" content="{{ auth()->check() ? 'true' : 'false' }}">
<meta name="login-url" content="{{ route('login') }}">
<meta name="quick-order-url" content="{{ route('orders.quick') }}">
<meta name="cart-url" content="{{ route('cart.add') }}">
<meta name="home-url" content="{{ route('home') }}">
<meta name="wa-number" content="{{ preg_replace('/[^0-9]/', '', setting('contact_whatsapp', '6282291409209')) }}">
<meta name="site-name" content="{{ setting('site_name', 'Zain Hanger') }}">
<meta name="product-name" content="{{ $product->name }}">

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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-start"
             x-data="productDetail(
                 {{ $product->priceTiers->toJson() }},
                 {{ $product->id }},
                 {{ json_encode($variants) }},
                 {{ json_encode($variantOptions) }},
                 {{ json_encode($images) }}
             )">

        {{-- KIRI: GALLERY --}}
        <div>
            <div style="border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-lg); aspect-ratio: 1/1;">
                <img :src="activeImage" alt="{{ $product->name }}" class="w-full h-full object-cover transition-all duration-300">
            </div>
            @if(count($images) > 1)
            <div class="flex gap-3 mt-4 flex-wrap">
                <template x-for="(img, i) in images" :key="i">
                    <div @click="activeImage = img.url"
                        class="cursor-pointer rounded-lg overflow-hidden border-2 transition-all"
                        :class="activeImage === img.url ? 'border-yellow-500' : 'border-transparent'"
                        style="width: 72px; height: 72px; flex-shrink: 0;">
                        <img :src="img.url" class="w-full h-full object-cover">
                    </div>
                </template>
            </div>
            @endif
        </div>

            {{-- KANAN: INFO --}}
            <div>
                <div class="flex gap-2 mb-4 flex-wrap">
                    <span class="text-xs font-bold px-3 py-1 rounded-full" style="background: var(--secondary); color: var(--primary)">
                        {{ $product->category->name ?? '' }}
                    </span>
                </div>

                <h1 class="text-3xl font-bold mb-4" style="color: var(--primary)">{{ $product->name }}</h1>

                @if($product->description)
                    <p class="mb-6" style="color: var(--text-muted)">{{ $product->description }}</p>
                @endif

                {{-- PILIH VARIASI --}}
                @if(count($variantOptions) > 0)
                <div class="mb-6">
                    @foreach($variantOptions as $option)
                    <div class="mb-4">
                        <label class="block text-xs font-bold mb-2" style="color: var(--text-muted)">
                            {{ strtoupper($option['name']) }}
                        </label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($option['values'] as $val)
                            <button
                                @click="selectVariantOption('{{ addslashes($option['name']) }}', '{{ addslashes($val['value']) }}', {{ json_encode($val['image_url'] ?? null) }})"
                                :class="selectedOptions['{{ addslashes($option['name']) }}'] === '{{ addslashes($val['value']) }}' ? 'border-2 font-bold' : 'border'"
                                :style="selectedOptions['{{ addslashes($option['name']) }}'] === '{{ addslashes($val['value']) }}'
                                    ? 'border-color: var(--secondary); background: #fffbeb; color: var(--primary);'
                                    : 'border-color: #ddd; color: var(--text-muted);'"
                                class="px-4 py-2 rounded-lg text-sm transition-all">
                                {{ $val['value'] }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                    <div x-show="selectedVariant" class="mt-2 text-xs" style="color: var(--text-muted)">
                        Dipilih: <span x-text="selectedVariantLabel" class="font-semibold" style="color: var(--primary)"></span>
                    </div>
                    <div x-show="!selectedVariant && variantOptions.length > 0" class="mt-2 text-xs text-orange-500">
                        ⚠️ Pilih semua variasi untuk melihat harga
                    </div>
                </div>
                @endif

                {{-- HARGA GROSIR --}}
                <div class="mb-6 rounded-xl overflow-hidden" style="border: 2px solid var(--secondary);">
                    <div class="px-5 py-3 flex items-center gap-2" style="background: var(--secondary);">
                        <span class="text-lg">🏷️</span>
                        <h3 class="font-bold" style="color: var(--primary)">Harga Grosir — Makin Banyak Makin Murah!</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <template x-for="(tier, i) in activePriceTiers" :key="i">
                            <div class="flex justify-between items-center px-5 py-3"
                                 :style="i === 0 ? 'background: var(--bg-color);' : ''">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold px-2 py-1 rounded-full"
                                          :style="i === 0 ? 'background: var(--primary); color: white;' : 'background: var(--secondary); color: var(--primary);'"
                                          x-text="i === 0 ? 'ECERAN' : 'GROSIR'"></span>
                                    <span class="text-sm" style="color: var(--text-muted);"
                                          x-text="tier.min_qty.toLocaleString('id') + (tier.max_qty ? '–' + tier.max_qty.toLocaleString('id') : '+') + ' pcs'"></span>
                                </div>
                                <div class="text-right">
                                    <span class="text-base font-bold" style="color: var(--primary)"
                                          x-text="'Rp ' + parseInt(tier.price).toLocaleString('id-ID')"></span>
                                    <span class="text-xs" style="color: var(--text-muted)">/pcs</span>
                                </div>
                            </div>
                        </template>
                        <div x-show="activePriceTiers.length === 0" class="px-5 py-4 text-sm text-center" style="color: var(--text-muted)">
                            Pilih variasi untuk melihat harga
                        </div>
                    </div>
                    <div class="px-5 py-3 text-xs" style="background: #fffbeb; color: var(--text-muted); border-top: 1px dashed var(--secondary);">
                        💡 Harga otomatis menyesuaikan jumlah order. Input qty di bawah untuk melihat total.
                    </div>
                </div>

                {{-- INPUT QTY --}}
                <div class="mb-6">
                    <label class="block text-xs font-bold mb-2" style="color: var(--text-muted)">JUMLAH ORDER (PCS)</label>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-3 border rounded-lg px-4 py-2" style="border-color: #e5e7eb;">
                            <button @click="decreaseQty()" class="text-xl font-bold w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100" style="color: var(--primary)">−</button>
                            <input type="number" x-model="qty" @input="calcPrice()" min="1"
                                   class="w-20 text-center text-lg font-semibold border-none outline-none"
                                   style="color: var(--primary)">
                            <button @click="increaseQty()" class="text-xl font-bold w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100" style="color: var(--primary)">+</button>
                        </div>
                        <span class="text-sm" style="color: var(--text-muted)">pcs</span>
                    </div>
                </div>

                {{-- KALKULASI --}}
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
                        <span class="text-white font-bold">Total Estimasi</span>
                        <span class="text-2xl font-bold" style="color: var(--secondary)" x-text="formatRupiah(totalPrice)"></span>
                    </div>
                    <div class="mt-2">
                        <span class="text-xs text-white/60" x-text="tierLabel"></span>
                    </div>
                </div>

                {{-- TOMBOL --}}
                <div class="flex flex-col gap-3">
                    <button @click="pesanSekarang()" :disabled="loading"
                            class="btn text-base py-4 w-full justify-center text-white"
                            style="background: #25d366; border-radius: var(--radius-md);">
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            Pesan Sekarang
                        </span>
                    </button>

                    <button @click="addToCart()"
                            class="btn text-base py-4 w-full justify-center"
                            style="border: 2px solid var(--primary); color: var(--primary); border-radius: var(--radius-md);"
                            onmouseover="this.style.background='var(--primary)';this.style.color='white'"
                            onmouseout="this.style.background='transparent';this.style.color='var(--primary)'">
                        🛒 Tambah ke Keranjang
                    </button>
                </div>

                <p class="text-xs mt-4 text-center" style="color: var(--text-muted)">
                    Klik <strong>Pesan Sekarang</strong> → isi data penerima → WhatsApp terbuka untuk konfirmasi
                </p>
            </div>

            {{-- FORM DATA PENERIMA --}}
            <div id="order-form-section" x-show="showOrderForm" x-transition
                 class="md:col-span-2 mt-4 max-w-xl mx-auto w-full">
                <div class="rounded-2xl p-8" style="border: 2px solid var(--secondary); background: var(--bg-color);">
                    <h2 class="text-xl font-bold mb-6" style="color: var(--primary)">📋 Data Penerima</h2>
                    <div class="flex flex-col gap-4">
                        <div>
                            <label class="block text-xs font-bold mb-1" style="color: var(--text-muted)">NAMA LENGKAP <span class="text-red-500">*</span></label>
                            <input type="text" x-model="recipientName" placeholder="Nama penerima"
                                   class="w-full px-4 py-3 rounded-lg border outline-none focus:ring-2"
                                   style="border-color: #e5e7eb; color: var(--primary)">
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1" style="color: var(--text-muted)">NOMOR HP / WHATSAPP <span class="text-red-500">*</span></label>
                            <input type="tel" x-model="recipientPhone" placeholder="08xxxxxxxxxx"
                                   class="w-full px-4 py-3 rounded-lg border outline-none focus:ring-2"
                                   style="border-color: #e5e7eb; color: var(--primary)">
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1" style="color: var(--text-muted)">ALAMAT PENGIRIMAN <span class="text-red-500">*</span></label>
                            <textarea x-model="recipientAddress" placeholder="Jalan, No. Rumah, Kota, Kode Pos..."
                                      rows="3" class="w-full px-4 py-3 rounded-lg border outline-none focus:ring-2"
                                      style="border-color: #e5e7eb; color: var(--primary)"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1" style="color: var(--text-muted)">CATATAN (OPSIONAL)</label>
                            <textarea x-model="recipientNote" placeholder="Warna, ukuran khusus, atau catatan lainnya..."
                                      rows="2" class="w-full px-4 py-3 rounded-lg border outline-none focus:ring-2"
                                      style="border-color: #e5e7eb; color: var(--primary)"></textarea>
                        </div>
                        <div class="text-xs p-3 rounded-lg" style="background: #fffbeb; color: var(--text-muted)">
                            ℹ️ Setelah submit, Anda akan diarahkan ke <strong>WhatsApp</strong> untuk konfirmasi pesanan dengan admin kami.
                        </div>
                        <button @click="submitOrder()" :disabled="loading"
                                class="btn text-base py-4 w-full justify-center text-white"
                                style="background: var(--primary); border-radius: var(--radius-md);">
                            <span x-show="!loading">Konfirmasi & Lanjut ke WhatsApp →</span>
                            <span x-show="loading">Memproses...</span>
                        </button>
                        <button @click="showOrderForm = false" class="text-sm text-center" style="color: var(--text-muted)">
                            ← Kembali
                        </button>
                    </div>
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
@endsection
