<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\ProductVariant;
use App\Models\ProductVariantPriceTier;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->record->load([
            'images',
            'variantOptions.values',
            'allVariants.priceTiers',
            'priceTiers',
        ]);

        $data['images'] = $record->images->map(fn($img) => [
            'id'         => $img->id,
            'image'      => $img->image,
            'image_url'  => $img->image_url,
            'sort_order' => $img->sort_order,
            'is_primary' => $img->is_primary,
        ])->toArray();

        $data['variantOptions'] = $record->variantOptions->map(fn($opt) => [
            'id'         => $opt->id,
            'name'       => $opt->name,
            'sort_order' => $opt->sort_order,
            'values'     => $opt->values->map(fn($val) => [
                'id'         => $val->id,
                'value'      => $val->value,
                'image'      => $val->image,
                'sort_order' => $val->sort_order,
            ])->toArray(),
        ])->toArray();

        $data['variants'] = $record->allVariants->map(fn($v) => [
            'id'                => $v->id,
            'sku'               => $v->sku,
            'combination_items' => collect($v->combination ?? [])
                ->map(fn($val, $key) => [
                    'option_name'  => $key,
                    'option_value' => $val,
                ])
                ->values()
                ->toArray(),
            'is_active'  => $v->is_active,
            'sort_order' => $v->sort_order,
            'priceTiers' => $v->priceTiers->map(fn($t) => [
                'id'      => $t->id,
                'min_qty' => $t->min_qty,
                'max_qty' => $t->max_qty,
                'price'   => $t->price,
            ])->toArray(),
        ])->toArray();

        $data['priceTiers'] = $record->priceTiers->map(fn($t) => [
            'id'      => $t->id,
            'min_qty' => $t->min_qty,
            'max_qty' => $t->max_qty,
            'price'   => $t->price,
        ])->toArray();

        return $data;
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        // Ambil variants sebelum dihapus dari data
        $variantsData = $data['variants'] ?? [];
        unset($data['variants']);

        // Save data produk utama
        $record->update($data);

        // Handle variants manual
        $existingIds = [];

        foreach ($variantsData as $variantData) {
            $combination = [];
            foreach ($variantData['combination_items'] ?? [] as $item) {
                if (!empty($item['option_name']) && !empty($item['option_value'])) {
                    $combination[$item['option_name']] = $item['option_value'];
                }
            }

            $priceTiersData = $variantData['priceTiers'] ?? [];

            if (!empty($variantData['id'])) {
                // Update existing
                $variant = ProductVariant::find($variantData['id']);
                if ($variant) {
                    $variant->update([
                        'sku'         => $variantData['sku'] ?? null,
                        'combination' => $combination,
                        'is_active'   => $variantData['is_active'] ?? true,
                        'sort_order'  => $variantData['sort_order'] ?? 0,
                    ]);
                    $existingIds[] = $variant->id;

                    // Update price tiers
                    $this->savePriceTiers($variant, $priceTiersData);
                }
            } else {
                // Create new
                $variant = ProductVariant::create([
                    'product_id'  => $record->id,
                    'sku'         => $variantData['sku'] ?? null,
                    'combination' => $combination,
                    'is_active'   => $variantData['is_active'] ?? true,
                    'sort_order'  => $variantData['sort_order'] ?? 0,
                ]);
                $existingIds[] = $variant->id;

                $this->savePriceTiers($variant, $priceTiersData);
            }
        }

        // Hapus variant yang dihapus dari form
        ProductVariant::where('product_id', $record->id)
            ->whereNotIn('id', $existingIds)
            ->delete();

        return $record;
    }

    protected function savePriceTiers(ProductVariant $variant, array $tiersData): void
    {
        $existingTierIds = [];

        foreach ($tiersData as $tierData) {
            if (!empty($tierData['id'])) {
                ProductVariantPriceTier::where('id', $tierData['id'])->update([
                    'min_qty' => $tierData['min_qty'],
                    'max_qty' => $tierData['max_qty'] ?? null,
                    'price'   => $tierData['price'],
                ]);
                $existingTierIds[] = $tierData['id'];
            } else {
                $tier = ProductVariantPriceTier::create([
                    'product_variant_id' => $variant->id,
                    'min_qty'            => $tierData['min_qty'],
                    'max_qty'            => $tierData['max_qty'] ?? null,
                    'price'              => $tierData['price'],
                ]);
                $existingTierIds[] = $tier->id;
            }
        }

        ProductVariantPriceTier::where('product_variant_id', $variant->id)
            ->whereNotIn('id', $existingTierIds)
            ->delete();
    }
}
