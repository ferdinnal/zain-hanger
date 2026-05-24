<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\ProductVariant;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    // Simpan combination_items sementara sebelum Filament save
    protected array $pendingCombinations = [];

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
            'combination'       => $v->combination,
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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Simpan combination_items ke property sebelum dihapus
        if (isset($data['variants'])) {
            foreach ($data['variants'] as $key => $variant) {
                $combination = [];
                if (isset($variant['combination_items'])) {
                    foreach ($variant['combination_items'] as $item) {
                        if (!empty($item['option_name']) && !empty($item['option_value'])) {
                            $combination[$item['option_name']] = $item['option_value'];
                        }
                    }
                }
                // Simpan sementara: pakai index atau id sebagai key
                $variantId = $variant['id'] ?? null;
                $this->pendingCombinations[$key] = [
                    'id'          => $variantId,
                    'combination' => $combination,
                ];

                // Set combination langsung di data juga
                $data['variants'][$key]['combination'] = !empty($combination) ? $combination : [];
                unset($data['variants'][$key]['combination_items']);
            }
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Update combination setelah Filament selesai save
        foreach ($this->pendingCombinations as $pending) {
            if (!empty($pending['id']) && !empty($pending['combination'])) {
                ProductVariant::where('id', $pending['id'])
                    ->update(['combination' => json_encode($pending['combination'])]);
            }
        }

        // Handle variant baru (belum punya id) — cari by product_id yang combination masih kosong
        $this->record->allVariants()
            ->where(function ($q) {
                $q->whereNull('combination')
                  ->orWhere('combination', '[]')
                  ->orWhere('combination', '{}')
                  ->orWhere('combination', 'null');
            })
            ->each(function ($variant) {
                // Cari di pendingCombinations yang tidak punya id (variant baru)
                foreach ($this->pendingCombinations as $pending) {
                    if (empty($pending['id']) && !empty($pending['combination'])) {
                        $variant->update(['combination' => json_encode($pending['combination'])]);
                        break;
                    }
                }
            });
    }
}
