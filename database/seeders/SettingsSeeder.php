<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ── SITE ─────────────────────────────────────────────
            ['key' => 'site_name',        'value' => 'Zain Hanger',   'group' => 'site', 'type' => 'text',     'label' => 'Nama Website'],
            ['key' => 'site_name_short',  'value' => 'ZAIN',          'group' => 'site', 'type' => 'text',     'label' => 'Nama Pendek (Logo)'],
            ['key' => 'site_name_sub',    'value' => 'HANGER',        'group' => 'site', 'type' => 'text',     'label' => 'Sub Nama (Logo)'],
            ['key' => 'site_logo',        'value' => null,            'group' => 'site', 'type' => 'image',    'label' => 'Logo'],
            ['key' => 'site_favicon',     'value' => null,            'group' => 'site', 'type' => 'image',    'label' => 'Favicon'],
            ['key' => 'site_description', 'value' => 'Penyedia hanger kayu premium dan perabot berkualitas tinggi untuk kebutuhan rumah tangga dan bisnis Anda.', 'group' => 'site', 'type' => 'textarea', 'label' => 'Deskripsi Meta'],
            ['key' => 'site_copyright',   'value' => '© 2026 Zain Hanger. All rights reserved.', 'group' => 'site', 'type' => 'text', 'label' => 'Copyright'],

            // ── HERO ─────────────────────────────────────────────
            ['key' => 'hero_image',         'value' => 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?q=80&w=2000', 'group' => 'hero', 'type' => 'image',    'label' => 'Gambar Hero'],
            ['key' => 'hero_subtitle',      'value' => 'Koleksi Premium Zain Hanger',          'group' => 'hero', 'type' => 'text',     'label' => 'Subjudul Hero'],
            ['key' => 'hero_title',         'value' => 'Solusi Gantungan Baju <br/><span>Elegan & Berkualitas</span>', 'group' => 'hero', 'type' => 'textarea', 'label' => 'Judul Hero (HTML)'],
            ['key' => 'hero_description',   'value' => 'Sediakan tampilan terbaik untuk lemari pakaian Anda dengan koleksi hanger kayu dan aksesoris perabot terbaik dari Zain Hanger.', 'group' => 'hero', 'type' => 'textarea', 'label' => 'Deskripsi Hero'],
            ['key' => 'hero_cta_primary',   'value' => 'Belanja Sekarang', 'group' => 'hero', 'type' => 'text', 'label' => 'Teks Tombol Utama'],
            ['key' => 'hero_cta_secondary', 'value' => 'Lihat Katalog',   'group' => 'hero', 'type' => 'text', 'label' => 'Teks Tombol Sekunder'],

            // ── ABOUT ────────────────────────────────────────────
            ['key' => 'about_title',    'value' => 'Kenapa Zain Hanger?',   'group' => 'about', 'type' => 'text',  'label' => 'Judul About'],
            ['key' => 'about_subtitle', 'value' => 'Kami mengutamakan kualitas dan estetika dalam setiap produk', 'group' => 'about', 'type' => 'text', 'label' => 'Subtitle About'],
            ['key' => 'about_image',    'value' => 'https://images.unsplash.com/photo-1594498653385-d5172c532c00?q=80&w=800', 'group' => 'about', 'type' => 'image', 'label' => 'Foto About'],
            ['key' => 'about_points',   'value' => json_encode([
                ['title' => 'Kualitas Kayu Pilihan',   'desc' => 'Setiap hanger kami dibuat dari kayu keras berkualitas tinggi yang dikeringkan sempurna untuk mencegah jamur dan pelapukan.'],
                ['title' => 'Finishing Halus',          'desc' => 'Proses sanding dan coating premium memastikan baju kesayangan Anda tidak akan tersangkut atau rusak.'],
                ['title' => 'Kustomisasi Tanpa Batas', 'desc' => 'Satu-satunya produsen hanger yang menawarkan kustomisasi warna, ukuran, dan jenis kepala hanger sesuai keinginan Anda.'],
            ]), 'group' => 'about', 'type' => 'json', 'label' => 'Poin About'],

            // ── CONTACT ──────────────────────────────────────────
            ['key' => 'contact_whatsapp', 'value' => '6282291409209', 'group' => 'contact', 'type' => 'text',     'label' => 'No. WhatsApp Admin'],
            ['key' => 'contact_email',    'value' => 'info@zainhanger.com', 'group' => 'contact', 'type' => 'text', 'label' => 'Email'],
            ['key' => 'contact_address',  'value' => 'Jalan Letjen Mashudi, Kel. Kersanagara, Kec. Cibeureum, Kota Tasikmalaya', 'group' => 'contact', 'type' => 'textarea', 'label' => 'Alamat'],

            // ── SOCIAL ───────────────────────────────────────────
            ['key' => 'social_instagram', 'value' => 'https://instagram.com/zainhanger', 'group' => 'social', 'type' => 'text', 'label' => 'Instagram'],
            ['key' => 'social_facebook',  'value' => '',                                 'group' => 'social', 'type' => 'text', 'label' => 'Facebook'],
            ['key' => 'social_tiktok',    'value' => 'https://tiktok.com/@zainhanger',   'group' => 'social', 'type' => 'text', 'label' => 'TikTok'],
            ['key' => 'social_shopee',    'value' => 'https://shopee.co.id/zainhanger',  'group' => 'social', 'type' => 'text', 'label' => 'Shopee'],

            // ── WA TEMPLATE ──────────────────────────────────────
            ['key' => 'wa_order_template', 'group' => 'wa', 'type' => 'textarea', 'label' => 'Template Pesan WA',
             'value' =>
                "Halo {site_name}, saya *{customer_name}* ingin memesan:\n\n" .
                "📦 *{product_name}*\n" .
                "Varian: {kepala} | {jenis}\n" .
                "Qty: {qty} pcs\n" .
                "Harga: {price_per_unit}/pcs\n" .
                "Total: {total}\n\n" .
                "Kode Order: #{order_code}\n" .
                "Mohon konfirmasinya 🙏"
            ],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(['key' => $s['key']], $s);
        }

        $this->command->info('✅ Settings seeded.');
    }
}
