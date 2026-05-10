@extends('layouts.app')
@php $pageTitle = 'Katalog Produk' @endphp

@section('content')
<section class="section-padding" style="padding-top: 120px;" id="catalog">
    <div class="container mx-auto px-5">
        <div class="section-header">
            <h2 class="section-title">Katalog Produk</h2>
            <p class="section-subtitle">Pilih variasi terbaik untuk kebutuhan Anda</p>
        </div>

        <div style="display: grid; grid-template-columns: 300px 1fr; gap: 40px;" class="catalog-layout">

            {{-- ===== SIDEBAR FILTER ===== --}}
            <aside class="filters-sidebar glass rounded-lg p-8" style="height: fit-content; position: sticky; top: 100px;">
                <h3 class="text-lg font-bold mb-6 pb-3 inline-block border-b-2" style="color: var(--primary); border-color: var(--secondary)">
                    Filter Produk
                </h3>

                <form method="GET" action="{{ route('catalog.index') }}" id="filter-form">

                    {{-- Search --}}
                    <div class="mb-5">
                        <label class="block text-xs font-bold mb-2" style="color: var(--text-muted)">CARI PRODUK</label>
                        <input type="text" name="q" value="{{ request('q') }}"
                               placeholder="Nama produk..."
                               class="filter-select"
                               oninput="this.form.submit()">
                    </div>

                    {{-- Kategori --}}
                    <div class="mb-5">
                        <label class="block text-xs font-bold mb-2" style="color: var(--text-muted)">KATEGORI</label>
                        <select name="category" class="filter-select" onchange="this.form.submit()">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Jenis (Hanya jika kategori Gantungan) --}}
                    <div class="mb-5">
                        <label class="block text-xs font-bold mb-2" style="color: var(--text-muted)">JENIS HANGER</label>
                        <select name="jenis" class="filter-select" onchange="this.form.submit()">
                            <option value="">Semua Jenis</option>
                            <option value="polos" {{ request('jenis') === 'polos' ? 'selected' : '' }}>Polos</option>
                            <option value="palang_kayu" {{ request('jenis') === 'palang_kayu' ? 'selected' : '' }}>Palang Kayu</option>
                            <option value="celana" {{ request('jenis') === 'celana' ? 'selected' : '' }}>Celana</option>
                            <option value="palang_jepit" {{ request('jenis') === 'palang_jepit' ? 'selected' : '' }}>Palang Jepit</option>
                            <option value="celana_palang_jepit" {{ request('jenis') === 'celana_palang_jepit' ? 'selected' : '' }}>Celana Palang Jepit</option>
                        </select>
                    </div>

                    {{-- Kepala --}}
                    <div class="mb-5">
                        <label class="block text-xs font-bold mb-2" style="color: var(--text-muted)">JENIS KEPALA</label>
                        <select name="kepala" class="filter-select" onchange="this.form.submit()">
                            <option value="">Semua Kepala</option>
                            <option value="silver" {{ request('kepala') === 'silver' ? 'selected' : '' }}>Hook Silver Biasa</option>
                            <option value="gold_10" {{ request('kepala') === 'gold_10' ? 'selected' : '' }}>Hook Gold 10cm</option>
                            <option value="gold_15" {{ request('kepala') === 'gold_15' ? 'selected' : '' }}>Hook Gold 15cm</option>
                            <option value="gold_20" {{ request('kepala') === 'gold_20' ? 'selected' : '' }}>Hook Gold 20cm</option>
                            <option value="plat_gold_10" {{ request('kepala') === 'plat_gold_10' ? 'selected' : '' }}>Plat Gold 10cm</option>
                            <option value="plat_gold_15" {{ request('kepala') === 'plat_gold_15' ? 'selected' : '' }}>Plat Gold 15cm</option>
                            <option value="plat_silver_10" {{ request('kepala') === 'plat_silver_10' ? 'selected' : '' }}>Plat Silver 10cm</option>
                        </select>
                    </div>

                    {{-- Anti Theft --}}
                    <div class="mb-5">
                        <label class="block text-xs font-bold mb-2" style="color: var(--text-muted)">TIPE</label>
                        <select name="type" class="filter-select" onchange="this.form.submit()">
                            <option value="">Semua Tipe</option>
                            <option value="standard" {{ request('type') === 'standard' ? 'selected' : '' }}>Standard</option>
                            <option value="anti_theft" {{ request('type') === 'anti_theft' ? 'selected' : '' }}>Anti Theft</option>
                        </select>
                    </div>

                    @if(request()->hasAny(['q','category','jenis','kepala','type']))
                        <a href="{{ route('catalog.index') }}"
                           class="block text-center text-sm mt-4 py-2 rounded"
                           style="color: var(--primary); border: 1px solid var(--primary);">
                            Reset Filter
                        </a>
                    @endif

                </form>

                <div class="mt-6 pt-4 border-t border-dashed border-gray-200">
                    <p class="text-xs italic" style="color: var(--text-muted)">
                        Menampilkan <strong>{{ $products->total() }}</strong> produk
                    </p>
                </div>
            </aside>

            {{-- ===== PRODUCT GRID ===== --}}
            <div>
                @if($products->isEmpty())
                    <div class="text-center py-20">
                        <div class="text-6xl mb-4">📭</div>
                        <h3 class="text-xl font-semibold mb-2" style="color: var(--primary)">Produk tidak ditemukan</h3>
                        <p style="color: var(--text-muted)">Coba ubah filter atau kata kunci pencarian</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-8">
                        @foreach($products as $product)
                            @include('public.catalog._product-card', ['product' => $product])
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-12">
                        {{ $products->withQueryString()->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</section>
@endsection
