import './bootstrap';
import Alpine from 'alpinejs';

// ── Product Detail Page ─────────────────────────────────────────
window.productDetail = function(defaultTiers, productId, variants, variantOptions, images) {
    return {
        productId, variants, variantOptions, images, defaultTiers,
        qty: 1, pricePerUnit: 0, totalPrice: 0, tierLabel: '',
        loading: false, selectedOptions: {}, selectedVariant: null,
        activeImage: images.length > 0 ? images[0].url : '',

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
            // Ganti activeImage kalau value ini punya gambar sendiri
            if (imageUrl) {
                this.activeImage = imageUrl;
            }
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
            navbar.style.boxShadow = window.scrollY > 50
                ? '0 4px 20px rgba(0,0,0,0.1)'
                : 'none';
        });
    }
});
