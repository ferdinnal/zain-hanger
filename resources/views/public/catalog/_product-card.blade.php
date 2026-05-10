{{-- Partial: _product-card.blade.php --}}
<div class="product-card" x-data="productCard({{ $product->id }}, {{ $product->priceTiers->toJson() }})">

    {{-- Image --}}
    <div class="relative" style="height: 250px;">
        <img src="{{ $product->image_url }}"
             alt="{{ $product->name }}"
             class="w-full h-full object-cover">
        <div class="product-badge">{{ strtoupper($product->category->name ?? '') }}</div>
        @if($product->is_anti_theft)
            <div class="absolute top-3 right-3 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">
                ANTI THEFT
            </div>
        @endif
    </div>

    {{-- Info --}}
    <div class="p-5">
        <h4 class="text-base font-semibold mb-1" style="color: var(--text-main)">{{ $product->name }}</h4>

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

        {{-- Qty Input + Harga dinamis --}}
        <div class="mb-4">
            <div class="flex items-center gap-3 mb-2">
                <label class="text-xs font-bold" style="color: var(--text-muted)">QTY:</label>
                <input type="number"
                       min="1"
                       value="1"
                       x-model="qty"
                       @input="calcPrice()"
                       class="w-20 text-sm text-center border border-gray-200 rounded px-2 py-1 outline-none focus:border-yellow-500">
                <span class="text-xs" style="color: var(--text-muted)">pcs</span>
            </div>

            {{-- Harga berdasarkan tier --}}
            <div class="flex items-baseline gap-2">
                <span class="text-lg font-bold" style="color: var(--primary)" x-text="formatRupiah(pricePerUnit)"></span>
                <span class="text-xs" style="color: var(--text-muted)">/pcs</span>
            </div>
            <div class="mt-1">
                <span class="text-xs font-bold" style="color: var(--text-muted)">Total: </span>
                <span class="text-sm font-bold" style="color: var(--secondary)" x-text="formatRupiah(totalPrice)"></span>
            </div>

            {{-- Tier indicator --}}
            <div class="mt-2">
                <span class="price-tier-badge" x-text="tierLabel"></span>
            </div>
        </div>

        {{-- Buttons --}}
        <div class="flex flex-col gap-2">
            {{-- Order via WA --}}
            <button @click="orderViaWA()" class="btn-wa">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                Pesan via WA
            </button>

            {{-- Tambah ke Keranjang --}}
            <button @click="addToCart()" class="w-full py-3 text-sm font-semibold rounded text-center transition-all"
                    style="border: 2px solid var(--primary); color: var(--primary);"
                    onmouseover="this.style.background='var(--primary)';this.style.color='white'"
                    onmouseout="this.style.background='transparent';this.style.color='var(--primary)'">
                🛒 Tambah ke Keranjang
            </button>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
function productCard(productId, priceTiers) {
    return {
        productId,
        priceTiers,
        qty: 1,
        pricePerUnit: 0,
        totalPrice: 0,
        tierLabel: '',

        init() { this.calcPrice(); },

        calcPrice() {
            const qty = parseInt(this.qty) || 1;
            let tier = null;

            // Sort tiers by min_qty ascending
            const sorted = [...this.priceTiers].sort((a,b) => a.min_qty - b.min_qty);

            for (const t of sorted) {
                if (qty >= t.min_qty && (t.max_qty === null || qty <= t.max_qty)) {
                    tier = t;
                }
            }

            if (!tier) tier = sorted[0];

            this.pricePerUnit = tier ? tier.price : 0;
            this.totalPrice   = this.pricePerUnit * qty;
            this.tierLabel    = tier ? `Tier: ${tier.min_qty.toLocaleString('id')}${tier.max_qty ? '–'+tier.max_qty.toLocaleString('id') : '+'} pcs` : '';
        },

        formatRupiah(val) {
            return 'Rp ' + parseInt(val).toLocaleString('id-ID');
        },

        orderViaWA() {
            const waNumber = '{{ preg_replace('/[^0-9]/', '', setting('contact_whatsapp', '6282291409209')) }}';
            const productName = document.querySelector(`[data-product-id="${this.productId}"] h4`)?.textContent || 'Produk';
            const message = `Halo {{ setting('site_name', 'Zain Hanger') }}, saya ingin memesan:\n\n📦 *${document.title.split('—')[0].trim()}*\nQty: ${this.qty} pcs\nHarga: ${this.formatRupiah(this.pricePerUnit)}/pcs\nTotal: ${this.formatRupiah(this.totalPrice)}\n\nMohon konfirmasinya 🙏`;
            window.open(`https://wa.me/${waNumber}?text=${encodeURIComponent(message)}`, '_blank');

            // Simpan order ke database
            fetch('{{ route('orders.quick') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    product_id: this.productId,
                    qty: this.qty,
                    price_per_unit: this.pricePerUnit,
                    total: this.totalPrice
                })
            });
        },

        addToCart() {
            @auth
            fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    product_id: this.productId,
                    qty: this.qty
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    // Update cart badge
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
@endonce
