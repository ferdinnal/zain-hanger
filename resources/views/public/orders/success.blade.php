{{-- ================================================================
     resources/views/public/orders/success.blade.php
     ================================================================ --}}
@extends('layouts.app')
@php $pageTitle = 'Pesanan Berhasil' @endphp

@section('content')
<div class="min-h-screen flex items-center justify-center py-20" style="background-color: var(--bg-color);">
    <div class="container mx-auto px-5 max-w-2xl text-center">

        {{-- Success animation --}}
        <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-8 fade-in"
             style="background: linear-gradient(135deg, var(--primary), var(--primary-light));">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="text-3xl font-bold mb-3 fade-in" style="color: var(--primary)">
            Pesanan Berhasil Dikirim! 🎉
        </h1>
        <p class="text-lg mb-2 fade-in" style="color: var(--text-muted)">
            Kode Order: <strong style="color: var(--primary)">{{ $order->order_code }}</strong>
        </p>
        <p class="mb-8 fade-in" style="color: var(--text-muted)">
            Pesanan Anda telah kami terima. Lanjutkan chat di WhatsApp untuk konfirmasi dan proses selanjutnya.
        </p>

        {{-- Order Summary --}}
        <div class="glass rounded-2xl p-8 mb-8 text-left">
            <h3 class="font-bold text-lg mb-4" style="color: var(--primary)">Ringkasan Pesanan</h3>
            <div class="space-y-3 mb-4">
                @foreach($order->items as $item)
                <div class="flex justify-between items-start py-2 border-b border-gray-100">
                    <div>
                        <p class="font-semibold text-sm">{{ $item->product_snapshot['name'] }}</p>
                        <p class="text-xs mt-1" style="color: var(--text-muted)">
                            {{ $item->product_snapshot['kepala_label'] ?? '' }}
                            @if($item->product_snapshot['jenis_label'] ?? null)
                                · {{ $item->product_snapshot['jenis_label'] }}
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold" style="color: var(--primary)">{{ $item->subtotal_formatted }}</p>
                        <p class="text-xs" style="color: var(--text-muted)">{{ $item->qty }} pcs</p>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="flex justify-between font-bold text-lg pt-2">
                <span>Total</span>
                <span style="color: var(--primary)">{{ $order->total_formatted }}</span>
            </div>
        </div>

        {{-- WA Button --}}
        <a href="{{ $waUrl }}"
           target="_blank"
           class="btn-wa inline-flex w-auto px-10 py-4 text-base rounded-lg mb-6"
           style="max-width: 320px; margin: 0 auto 24px;">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
            Lanjut Chat di WhatsApp
        </a>

        <div class="flex gap-4 justify-center">
            <a href="{{ route('orders.index') }}" class="text-sm font-medium hover:underline" style="color: var(--primary)">
                Lihat Semua Pesanan
            </a>
            <span style="color: var(--text-muted)">·</span>
            <a href="{{ route('catalog.index') }}" class="text-sm font-medium hover:underline" style="color: var(--primary)">
                Lanjut Belanja
            </a>
        </div>

    </div>
</div>

{{-- Auto redirect ke WA setelah 2 detik --}}
@push('scripts')
<script>
    setTimeout(() => {
        window.open('{{ $waUrl }}', '_blank');
    }, 1500);
</script>
@endpush
@endsection


{{-- ================================================================
     resources/views/public/checkout/index.blade.php
     ================================================================ --}}
@extends('layouts.app')
@php $pageTitle = 'Checkout' @endphp

@section('content')
<section class="section-padding" style="padding-top: 120px;">
    <div class="container mx-auto px-5 max-w-5xl">
        <div class="section-header">
            <h2 class="section-title">Checkout</h2>
            <p class="section-subtitle">Lengkapi data pengiriman untuk menyelesaikan pesanan</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

            {{-- Form Checkout --}}
            <div>
                <form method="POST" action="{{ route('checkout.store') }}" class="space-y-5">
                    @csrf

                    <div class="glass rounded-xl p-6">
                        <h3 class="font-bold text-lg mb-5" style="color: var(--primary)">Data Penerima</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold mb-2" style="color: var(--text-muted)">NAMA LENGKAP</label>
                                <input type="text" name="customer_name"
                                       value="{{ old('customer_name', auth()->user()->name) }}"
                                       class="filter-select" required>
                                @error('customer_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold mb-2" style="color: var(--text-muted)">NOMOR HP / WHATSAPP</label>
                                <input type="tel" name="customer_phone"
                                       value="{{ old('customer_phone', auth()->user()->phone) }}"
                                       placeholder="08xxxxxxxxx"
                                       class="filter-select" required>
                                @error('customer_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold mb-2" style="color: var(--text-muted)">ALAMAT PENGIRIMAN</label>
                                <textarea name="shipping_address" rows="3"
                                          placeholder="Jalan, No. Rumah, Kota, Kode Pos..."
                                          class="filter-select">{{ old('shipping_address', auth()->user()->address) }}</textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-bold mb-2" style="color: var(--text-muted)">CATATAN (OPSIONAL)</label>
                                <textarea name="notes" rows="2"
                                          placeholder="Warna, ukuran khusus, atau catatan lainnya..."
                                          class="filter-select">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="glass rounded-xl p-5 text-sm" style="color: var(--text-muted)">
                        <p>ℹ️ Setelah submit, Anda akan diarahkan ke <strong>WhatsApp</strong> untuk konfirmasi pesanan dengan admin kami.</p>
                    </div>

                    <button type="submit" class="btn btn-primary w-full justify-center text-base py-4">
                        Konfirmasi & Lanjut ke WhatsApp →
                    </button>
                </form>
            </div>

            {{-- Order Summary --}}
            <div class="glass rounded-xl p-6 h-fit">
                <h3 class="font-bold text-lg mb-5" style="color: var(--primary)">Ringkasan Pesanan</h3>

                <div class="space-y-4 mb-5">
                    @foreach($cartItems as $item)
                    <div class="flex gap-3 items-start pb-4 border-b border-gray-100">
                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}"
                             class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                        <div class="flex-1">
                            <p class="font-semibold text-sm">{{ $item->product->name }}</p>
                            <p class="text-xs mt-1" style="color: var(--text-muted)">{{ $item->qty }} pcs</p>
                        </div>
                        <p class="font-bold text-sm" style="color: var(--primary)">{{ $item->subtotal_formatted }}</p>
                    </div>
                    @endforeach
                </div>

                <div class="flex justify-between font-bold text-lg pt-2">
                    <span>Total</span>
                    <span style="color: var(--primary)">{{ $totalFormatted }}</span>
                </div>
                <p class="text-xs mt-2" style="color: var(--text-muted)">*Ongkos kirim akan dikonfirmasi admin via WhatsApp</p>
            </div>

        </div>
    </div>
</section>
@endsection
