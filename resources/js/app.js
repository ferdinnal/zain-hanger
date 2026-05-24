import './bootstrap';
import Alpine from 'alpinejs';

// ── Product Detail Page ─────────────────────────────────────────

window.productDetail = function(defaultTiers, productId, variants, variantOptions, images) {
    return {
        productId, variants, variantOptions, images, defaultTiers,
        qty: 1, pricePerUnit: 0, totalPrice: 0, tierLabel: '',
        loading: false, selectedOptions: {}, selectedVariant: null,
        activeImage: images.length > 0 ? images[0].url : '',
        isLoggedIn: false,
        loginUrl: '',
        quickOrderUrl: '',
        cartUrl: '',
        csrfToken: '',
        waNumber: '',
        siteName: '',

        get activePriceTiers() {
            return this.selectedVariant
                ? (this.selectedVariant.price_tiers ?? [])
                : (this.defaultTiers ?? []);
        },

        get selectedVariantLabel() {
            if (!this.selectedVariant) return '';
            return Object.entries(this.selectedVariant.combination)
                .map(([k, v]) => `${k}: ${v}`).join(' | ');
        },

        init() {
            // Ambil config dari meta tag yang di-render blade
            this.isLoggedIn  = document.querySelector('meta[name="auth-check"]')?.content === 'true';
            this.loginUrl    = document.querySelector('meta[name="login-url"]')?.content ?? '';
            this.quickOrderUrl = document.querySelector('meta[name="quick-order-url"]')?.content ?? '';
            this.cartUrl     = document.querySelector('meta[name="cart-url"]')?.content ?? '';
            this.csrfToken   = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            this.waNumber    = document.querySelector('meta[name="wa-number"]')?.content ?? '';
            this.siteName    = document.querySelector('meta[name="site-name"]')?.content ?? '';

            this.variantOptions.forEach(opt => {
                if (opt.values && opt.values.length === 1) {
                    this.selectedOptions[opt.name] = opt.values[0].value;
                }
            });
            this.matchVariant();
            this.calcPrice();
        },

        selectVariantOption(name, value, imageUrl) {
            this.selectedOptions = { ...this.selectedOptions, [name]: value };
            if (imageUrl) this.activeImage = imageUrl;
            this.matchVariant();
            this.calcPrice();
        },

        matchVariant() {
            if (this.variantOptions.length === 0) { this.selectedVariant = null; return; }
            const allSelected = this.variantOptions.every(
                opt => this.selectedOptions[opt.name] !== undefined
            );
            if (!allSelected) { this.selectedVariant = null; return; }
            this.selectedVariant = this.variants.find(v =>
                this.variantOptions.every(
                    opt => v.combination[opt.name] === this.selectedOptions[opt.name]
                )
            ) ?? null;
        },

        calcPrice() {
            const qty = parseInt(this.qty) || 1;
            const tiers = this.activePriceTiers;
            if (!tiers || tiers.length === 0) {
                this.pricePerUnit = 0; this.totalPrice = 0; this.tierLabel = '';
                return;
            }
            const sorted = [...tiers].sort((a, b) => a.min_qty - b.min_qty);
            let tier = sorted[0];
            for (const t of sorted) {
                if (qty >= t.min_qty && (t.max_qty === null || qty <= t.max_qty)) tier = t;
            }
            this.pricePerUnit = tier ? parseFloat(tier.price) : 0;
            this.totalPrice   = this.pricePerUnit * qty;
            this.tierLabel    = tier
                ? `Tier ${tier.min_qty.toLocaleString('id')}${tier.max_qty ? '–' + tier.max_qty.toLocaleString('id') : '+'} pcs`
                : '';
        },

        increaseQty() { this.qty = parseInt(this.qty) + 1; this.calcPrice(); },
        decreaseQty() { if (parseInt(this.qty) > 1) { this.qty = parseInt(this.qty) - 1; this.calcPrice(); } },
        formatRupiah(val) { return 'Rp ' + parseInt(val || 0).toLocaleString('id-ID'); },

        async pesanSekarang() {
            if (!this.isLoggedIn) { window.location.href = this.loginUrl; return; }
            if (this.variantOptions.length > 0 && !this.selectedVariant) {
                alert('Pilih semua variasi terlebih dahulu!'); return;
            }
            this.loading = true;
            try {
                const res = await fetch(this.quickOrderUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
                    body: JSON.stringify({
                        product_id:     this.productId,
                        qty:            parseInt(this.qty),
                        price_per_unit: this.pricePerUnit,
                        total:          this.totalPrice,
                        variant_label:  this.selectedVariantLabel,
                    })
                });
                const data = await res.json();
                if (data.redirect) { window.location.href = data.redirect; return; }
                const productName = document.querySelector('meta[name="product-name"]')?.content ?? '';
                const variantText = this.selectedVariant
                    ? '\n' + Object.entries(this.selectedVariant.combination).map(([k,v]) => `${k}: ${v}`).join('\n')
                    : '';
                const message =
                    `Halo ${this.siteName}, saya ingin memesan:\n\n` +
                    `📦 *${productName}*` + variantText + '\n' +
                    `Qty: ${parseInt(this.qty)} pcs\n` +
                    `Harga: ${this.formatRupiah(this.pricePerUnit)}/pcs\n` +
                    `Total: ${this.formatRupiah(this.totalPrice)}\n\n` +
                    `🔖 Kode Order: *${data.order_code ?? '-'}*\nMohon konfirmasinya 🙏`;
                window.open(`https://wa.me/${this.waNumber}?text=${encodeURIComponent(message)}`, '_blank');
            } catch(e) {
                console.error('Order error:', e);
                const productName = document.querySelector('meta[name="product-name"]')?.content ?? '';
                window.open(`https://wa.me/${this.waNumber}?text=${encodeURIComponent('Halo, saya ingin memesan ' + productName + '. Mohon konfirmasinya 🙏')}`, '_blank');
            } finally {
                this.loading = false;
            }
        },

        addToCart() {
            if (!this.isLoggedIn) { window.location.href = this.loginUrl; return; }
            if (this.variantOptions.length > 0 && !this.selectedVariant) {
                alert('Pilih semua variasi terlebih dahulu!'); return;
            }
            fetch(this.cartUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
                body: JSON.stringify({
                    product_id: this.productId,
                    qty:        parseInt(this.qty),
                    variant_id: this.selectedVariant?.id ?? null,
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const badge = document.querySelector('.cart-badge');
                    if (badge) badge.textContent = data.cart_count;
                    alert('Produk berhasil ditambahkan ke keranjang!');
                }
            })
            .catch(e => console.error('Cart error:', e));
        }
    };
};

// ── Product Card ────────────────────────────────────────────────
window.productCard = function(productId, priceTiers) {
    return {
        productId, priceTiers,
        qty: 1, pricePerUnit: 0, totalPrice: 0, tierLabel: '',
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
        formatRupiah(val) { return 'Rp ' + parseInt(val).toLocaleString('id-ID'); },
    };
};

window.Alpine = Alpine;
Alpine.start();

// Navbar scroll
document.addEventListener('DOMContentLoaded', () => {
    const navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            navbar.style.boxShadow = window.scrollY > 50 ? '0 4px 20px rgba(0,0,0,0.1)' : 'none';
        });
    }
});
