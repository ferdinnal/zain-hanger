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
                        ℹ️ Setelah submit, Anda akan diarahkan ke <strong>WhatsApp</strong> untuk konfirmasi pesanan dengan admin kami.
                    </div>

                    <button type="submit" class="btn btn-primary w-full justify-center text-base py-4">
                        Konfirmasi & Lanjut ke WhatsApp →
                    </button>
                </form>
            </div>

            {{-- Order Summary --}}
            <div class="glass rounded-xl p-6 h-fit sticky top-24">
                <h3 class="font-bold text-lg mb-5 pb-3 border-b" style="color: var(--primary); border-color: var(--secondary)">
                    Ringkasan Pesanan
                </h3>

                <div class="space-y-4 mb-5">
                    @foreach($cartItems as $item)
                    <div class="flex gap-3 items-start pb-4 border-b border-gray-100">
                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}"
                             class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                        <div class="flex-1">
                            <p class="font-semibold text-sm">{{ $item->product->name }}</p>
                            <p class="text-xs mt-1" style="color: var(--text-muted)">{{ $item->qty }} pcs</p>
                        </div>
                        <p class="font-bold text-sm flex-shrink-0" style="color: var(--primary)">
                            {{ $item->subtotal_formatted }}
                        </p>
                    </div>
                    @endforeach
                </div>

                <div class="flex justify-between font-bold text-lg">
                    <span>Total</span>
                    <span style="color: var(--primary)">{{ $totalFormatted }}</span>
                </div>
                <p class="text-xs mt-2" style="color: var(--text-muted)">
                    *Ongkos kirim dikonfirmasi admin via WhatsApp
                </p>
            </div>

        </div>
    </div>
</section>
@endsection
