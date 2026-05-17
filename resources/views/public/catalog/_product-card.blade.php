{{-- Partial: _product-card.blade.php --}}
<div class="product-card">

    {{-- Image - klik ke detail --}}
    <a href="{{ route('catalog.show', $product->slug) }}" class="block relative" style="height: 250px;">
        <img src="{{ $product->images->first()?->url ?? $product->image_url }}"
             alt="{{ $product->name }}"
             class="w-full h-full object-cover">
        <div class="product-badge">{{ strtoupper($product->category->name ?? '') }}</div>
        @if($product->is_anti_theft)
            <div class="absolute top-3 right-3 text-white text-xs font-bold px-2 py-1 rounded" style="background: #ef4444;">
                ANTI THEFT
            </div>
        @endif
    </a>

    {{-- Info --}}
    <div class="p-5">
        <a href="{{ route('catalog.show', $product->slug) }}">
            <h4 class="text-base font-semibold mb-2 hover:underline" style="color: var(--text-main)">
                {{ $product->name }}
            </h4>
        </a>

        {{-- Kepala & Jenis badge --}}
        <div class="flex gap-2 flex-wrap mb-3">
            @if($product->kepala_label)
                <span class="text-xs px-2 py-1 rounded" style="background: var(--bg-color); color: var(--text-muted); border: 1px solid #ddd;">
                    {{ $product->kepala_label }}
                </span>
            @endif
            @if($product->jenis_label)
                <span class="text-xs px-2 py-1 rounded" style="background: var(--bg-color); color: var(--text-muted); border: 1px solid #ddd;">
                    {{ $product->jenis_label }}
                </span>
            @endif
        </div>

        {{-- Harga mulai --}}
        @php
            $minTier = $product->priceTiers->sortBy('min_qty')->first();
        @endphp
        @if($minTier)
            <div class="mb-4">
                <span class="text-xs" style="color: var(--text-muted)">Mulai dari</span>
                <div class="text-lg font-bold" style="color: var(--primary)">
                    Rp {{ number_format($minTier->price, 0, ',', '.') }}<span class="text-sm font-normal">/pcs</span>
                </div>
            </div>
        @endif

        {{-- Tombol Lihat Detail --}}
        <a href="{{ route('catalog.show', $product->slug) }}"
           class="btn-wa block text-center">
            🔍 Lihat Detail & Pesan
        </a>
    </div>
</div>
