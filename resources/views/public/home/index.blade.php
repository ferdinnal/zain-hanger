@extends('layouts.app')
@section('content')

{{-- ===== HERO SECTION ===== --}}
<section class="hero"

        @php
        $heroImage = setting('hero_image');
        $heroImageUrl = $heroImage
            ? (str_starts_with($heroImage, 'http') ? $heroImage : Storage::url($heroImage))
            : 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?q=80&w=2000';
    @endphp
    style="background-image: linear-gradient(rgba(0,0,0,0.5),rgba(0,0,0,0.5)), url('{{ $heroImageUrl }}')"<div class="hero-overlay"></div>

    <div class="container mx-auto px-5 hero-content fade-in">
        <h2 class="text-sm font-normal mb-4 uppercase tracking-widest" style="color: var(--secondary)">
            {{ setting('hero_subtitle', 'Koleksi Premium Zain Hanger') }}
        </h2>
        <h1 class="hero-title">
            {!! setting('hero_title', 'Solusi Gantungan Baju <br/><span>Elegan & Berkualitas</span>') !!}
        </h1>
        <p class="text-lg mb-10 opacity-90 max-w-xl">
            {{ setting('hero_description', 'Sediakan tampilan terbaik untuk lemari pakaian Anda dengan koleksi hanger kayu dan aksesoris perabot terbaik.') }}
        </p>
        <div class="flex gap-5 flex-wrap">
            <a href="{{ route('catalog.index') }}" class="btn btn-primary">
                {{ setting('hero_cta_primary', 'Belanja Sekarang') }}
            </a>
            <a href="#categories" class="btn btn-outline">
                {{ setting('hero_cta_secondary', 'Lihat Katalog') }}
            </a>
        </div>
    </div>

    {{-- Feature Bar --}}
    <div class="feature-bar container mx-auto">
        <div class="flex items-center gap-4">
            <span class="text-4xl">✨</span>
            <div>
                <h3 class="text-base font-bold">Kualitas Tinggi</h3>
                <p class="text-sm text-gray-500">Bahan kayu & metal pilihan</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-4xl">🚚</span>
            <div>
                <h3 class="text-base font-bold">Pengiriman Cepat</h3>
                <p class="text-sm text-gray-500">Seluruh Indonesia</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-4xl">📞</span>
            <div>
                <h3 class="text-base font-bold">Layanan 24/7</h3>
                <p class="text-sm text-gray-500">Konsultasi via WhatsApp</p>
            </div>
        </div>
    </div>
</section>

{{-- ===== CATEGORY PREVIEW ===== --}}
<section class="section-padding" id="categories" style="padding-top: 160px;">
    <div class="container mx-auto px-5">
        <div class="section-header">
            <h2 class="section-title">Kategori Utama</h2>
            <p class="section-subtitle">Temukan produk terbaik sesuai kebutuhan Anda</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($categories as $category)
            <a href="{{ route('catalog.index', ['category' => $category->slug]) }}" class="category-card block">
                <div class="category-img-wrapper" style="height: 400px; position: relative;">
                    <img src="{{ $category->image_url }}" alt="{{ $category->name }}"
                         style="width:100%;height:100%;object-fit:cover;transition:transform 0.4s ease;">
                    <div class="category-overlay">
                        <h3 class="text-2xl font-bold mb-2">{{ $category->name }}</h3>
                        <p class="text-sm opacity-80 mb-4">{{ $category->description }}</p>
                        <span style="color: var(--secondary); font-weight: 600;">Lihat Produk →</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== ABOUT SECTION ===== --}}
<section class="section-padding" id="about" style="background-color: #f5f0eb;">
    <div class="container mx-auto px-5">
        <div class="section-header">
            <h2 class="section-title">{{ setting('about_title', 'Kenapa Zain Hanger?') }}</h2>
            <p class="section-subtitle">{{ setting('about_subtitle', 'Kami mengutamakan kualitas dan estetika dalam setiap produk') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
            <div>
                @php
                    $aboutPoints = json_decode(setting('about_points', json_encode([
                        ['title' => 'Kualitas Kayu Pilihan', 'desc' => 'Setiap hanger kami dibuat dari kayu keras berkualitas tinggi yang dikeringkan dengan sempurna untuk mencegah jamur dan pelapukan.'],
                        ['title' => 'Finishing Halus', 'desc' => 'Proses sanding dan coating premium memastikan baju kesayangan Anda tidak akan tersangkut atau rusak.'],
                        ['title' => 'Kustomisasi Tanpa Batas', 'desc' => 'Satu-satunya produsen hanger yang menawarkan kustomisasi warna, ukuran, dan jenis kepala hanger sesuai keinginan Anda.'],
                    ])), true)
                @endphp

                @foreach($aboutPoints as $point)
                <div class="mb-8">
                    <h3 class="text-xl font-semibold mb-2" style="color: var(--primary)">{{ $point['title'] }}</h3>
                    <p style="color: var(--text-muted)">{{ $point['desc'] }}</p>
                </div>
                @endforeach
            </div>

            <div>
                @php
                    $aboutImage = setting('about_image');
                    $aboutImageUrl = $aboutImage
                        ? (str_starts_with($aboutImage, 'http') ? $aboutImage : \Storage::url($aboutImage))
                        : 'https://images.unsplash.com/photo-1594498653385-d5172c532c00?q=80&w=800';
                @endphp
                <img src="{{ $aboutImageUrl }}"
                    alt="Workshop Zain Hanger"
                    class="w-full object-cover"
                    style="border-radius: var(--radius-lg); box-shadow: var(--shadow-lg);">
            </div>
        </div>
    </div>
</section>

{{-- ===== FEATURED PRODUCTS ===== --}}
@if($featuredProducts->count() > 0)
<section class="section-padding">
    <div class="container mx-auto px-5">
        <div class="section-header">
            <h2 class="section-title">Produk Unggulan</h2>
            <p class="section-subtitle">Pilihan terbaik yang paling banyak diminati</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($featuredProducts as $product)
                @include('public.catalog._product-card', ['product' => $product])
            @endforeach
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('catalog.index') }}" class="btn btn-primary">Lihat Semua Produk</a>
        </div>
    </div>
</section>
@endif

@endsection
