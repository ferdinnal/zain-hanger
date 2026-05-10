<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $gantungan = Category::where('slug', 'gantungan')->firstOrFail();
        $imgHanger = 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?q=80&w=400';

        /**
         * Format tiap entry:
         * [nama, jenis, kepala, is_anti_theft, tiers[[min, max|null, harga], ...]]
         */
        $products = [

            // ── HALAMAN 1: SILVER / STANDARD ─────────────────────────
            ['Hanger Polos Dewasa Grade A',   'polos',       'silver', false, [[1,99,3500],[100,999,3400],[1000,9999,3200],[10000,null,3000]]],
            ['Hanger Palang Kayu Dewasa',     'palang_kayu', 'silver', false, [[1,99,4500],[100,999,4400],[1000,9999,4200],[10000,null,4000]]],
            ['Hanger Celana Dewasa',          'celana',      'silver', false, [[1,99,7500],[100,999,7000],[1000,9999,6800],[10000,null,6500]]],
            ['Hanger Palang Jepit Dewasa',    'palang_jepit','silver', false, [[1,99,8800],[100,999,8400],[1000,9999,8200],[10000,null,8000]]],
            ['Hanger Celana Palang Jepit',    'celana_palang_jepit','silver', false, [[1,99,9000],[100,999,8500],[1000,9999,8000],[10000,null,7800]]],

            // ── HALAMAN 1: SILVER / ANTI THEFT ───────────────────────
            ['Hanger Polos Dewasa Anti Theft',         'polos',       'silver', true, [[1,99,8500],[100,999,7500],[1000,9999,7000],[10000,null,6700]]],
            ['Hanger Palang Kayu Dewasa Anti Theft',   'palang_kayu', 'silver', true, [[1,99,9500],[100,999,8500],[1000,9999,8000],[10000,null,7700]]],
            ['Hanger Palang Jepit Dewasa Anti Theft',  'palang_jepit','silver', true, [[1,99,12500],[100,999,12000],[1000,9999,11500],[10000,null,11000]]],

            // ── HALAMAN 2: HOOK GOLD 10CM ────────────────────────────
            ['Hanger Polos Kepala Gold 10cm',          'polos',       'gold_10', false, [[1,99,5500],[100,999,5000],[1000,9999,4700],[10000,null,4500]]],
            ['Hanger Palang Kayu Kepala Gold 10cm',    'palang_kayu', 'gold_10', false, [[1,99,6500],[100,999,6000],[1000,9999,5700],[10000,null,5500]]],
            ['Hanger Celana Kepala Gold 10cm',         'celana',      'gold_10', false, [[1,99,11000],[100,999,10500],[1000,9999,10000],[10000,null,9800]]],
            ['Hanger Palang Jepit Kepala Gold 10cm',   'palang_jepit','gold_10', false, [[1,99,14000],[100,999,13500],[1000,9999,13000],[10000,null,12500]]],
            ['Hanger Celana Palang Jepit Gold 10cm',   'celana_palang_jepit','gold_10', false, [[1,99,14000],[100,999,13500],[1000,9999,13000],[10000,null,12500]]],

            // ── HALAMAN 3: HOOK GOLD 15CM ────────────────────────────
            ['Hanger Polos Kepala Gold 15cm',          'polos',       'gold_15', false, [[1,99,7000],[100,999,6500],[1000,9999,6300],[10000,null,6000]]],
            ['Hanger Palang Kayu Kepala Gold 15cm',    'palang_kayu', 'gold_15', false, [[1,99,8000],[100,999,7500],[1000,9999,7300],[10000,null,7000]]],
            ['Hanger Celana Kepala Gold 15cm',         'celana',      'gold_15', false, [[1,99,12500],[100,999,11800],[1000,9999,11300],[10000,null,11000]]],
            ['Hanger Palang Jepit Kepala Gold 15cm',   'palang_jepit','gold_15', false, [[1,99,15500],[100,999,14800],[1000,9999,14300],[10000,null,14000]]],
            ['Hanger Celana Palang Jepit Gold 15cm',   'celana_palang_jepit','gold_15', false, [[1,99,14500],[100,999,14000],[1000,9999,13800],[10000,null,13500]]],

            // ── HALAMAN 4: HOOK GOLD 20CM ────────────────────────────
            ['Hanger Polos Kepala Gold 20cm',          'polos',       'gold_20', false, [[1,99,7500],[100,999,7000],[1000,9999,6800],[10000,null,6500]]],
            ['Hanger Palang Kayu Kepala Gold 20cm',    'palang_kayu', 'gold_20', false, [[1,99,8500],[100,999,8000],[1000,9999,7800],[10000,null,7500]]],
            ['Hanger Celana Kepala Gold 20cm',         'celana',      'gold_20', false, [[1,99,13000],[100,999,12300],[1000,9999,11800],[10000,null,11500]]],
            ['Hanger Palang Jepit Kepala Gold 20cm',   'palang_jepit','gold_20', false, [[1,99,16000],[100,999,15300],[1000,9999,14800],[10000,null,14300]]],
            ['Hanger Celana Palang Jepit Gold 20cm',   'celana_palang_jepit','gold_20', false, [[1,99,15000],[100,999,14500],[1000,9999,14300],[10000,null,14000]]],

            // ── HALAMAN 5: PLAT GOLD 10CM ────────────────────────────
            ['Hanger Polos Plat Gold 10cm',            'polos',       'plat_gold_10', false, [[1,99,7500],[100,999,7000],[1000,9999,6800],[10000,null,6500]]],
            ['Hanger Palang Kayu Plat Gold 10cm',      'palang_kayu', 'plat_gold_10', false, [[1,99,8500],[100,999,8000],[1000,9999,7800],[10000,null,7500]]],
            ['Hanger Celana Plat Gold 10cm',           'celana',      'plat_gold_10', false, [[1,99,13500],[100,999,12800],[1000,9999,12300],[10000,null,12000]]],
            ['Hanger Palang Jepit Plat Gold 10cm',     'palang_jepit','plat_gold_10', false, [[1,99,16500],[100,999,15800],[1000,9999,15300],[10000,null,14800]]],
            ['Hanger Celana Palang Jepit Plat Gold 10cm','celana_palang_jepit','plat_gold_10', false, [[1,99,15500],[100,999,15000],[1000,9999,14800],[10000,null,14500]]],

            // ── HALAMAN 6: PLAT GOLD 15CM ────────────────────────────
            ['Hanger Polos Plat Gold 15cm',            'polos',       'plat_gold_15', false, [[1,99,8000],[100,999,7500],[1000,9999,7300],[10000,null,7000]]],
            ['Hanger Palang Kayu Plat Gold 15cm',      'palang_kayu', 'plat_gold_15', false, [[1,99,9000],[100,999,8500],[1000,9999,8300],[10000,null,8000]]],
            ['Hanger Celana Plat Gold 15cm',           'celana',      'plat_gold_15', false, [[1,99,13000],[100,999,12300],[1000,9999,11800],[10000,null,11500]]],
            ['Hanger Palang Jepit Plat Gold 15cm',     'palang_jepit','plat_gold_15', false, [[1,99,16000],[100,999,15300],[1000,9999,14800],[10000,null,14300]]],
            ['Hanger Celana Palang Jepit Plat Gold 15cm','celana_palang_jepit','plat_gold_15', false, [[1,99,15000],[100,999,14500],[1000,9999,14300],[10000,null,14000]]],

            // ── HALAMAN 7: PLAT SILVER 10CM ──────────────────────────
            ['Hanger Polos Plat Silver 10cm',          'polos',       'plat_silver_10', false, [[1,99,6000],[100,999,5800],[1000,9999,5700],[10000,null,5500]]],
            ['Hanger Palang Kayu Plat Silver 10cm',    'palang_kayu', 'plat_silver_10', false, [[1,99,7000],[100,999,6800],[1000,9999,6700],[10000,null,6500]]],
            ['Hanger Celana Plat Silver 10cm',         'celana',      'plat_silver_10', false, [[1,99,9500],[100,999,9400],[1000,9999,9300],[10000,null,9000]]],
            ['Hanger Palang Jepit Plat Silver 10cm',   'palang_jepit','plat_silver_10', false, [[1,99,10500],[100,999,10400],[1000,9999,10300],[10000,null,10000]]],
            ['Hanger Celana Palang Jepit Plat Silver 10cm','celana_palang_jepit','plat_silver_10', false, [[1,99,10500],[100,999,10400],[1000,9999,10300],[10000,null,10000]]],
        ];

        $count = 0;
        foreach ($products as $i => [$name, $jenis, $kepala, $antiTheft, $tiers]) {
            $slug = Str::slug($name);

            // Handle slug conflict
            $originalSlug = $slug;
            $j = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $j++;
            }

            $product = Product::create([
                'category_id'   => $gantungan->id,
                'name'          => $name,
                'slug'          => $slug,
                'jenis'         => $jenis,
                'kepala'        => $kepala,
                'is_anti_theft' => $antiTheft,
                'image_url'     => $imgHanger,
                'is_active'     => true,
                'is_featured'   => $i < 4,
                'sort_order'    => $i + 1,
            ]);

            foreach ($tiers as [$min, $max, $price]) {
                $product->priceTiers()->create([
                    'min_qty' => $min,
                    'max_qty' => $max,
                    'price'   => $price,
                ]);
            }

            $count++;
        }

        $this->command->info("✅ Products seeded ({$count} produk, " . ($count * 4) . " price tiers).");
    }
}
