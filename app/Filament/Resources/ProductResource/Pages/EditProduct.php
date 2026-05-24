<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
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
            'id'         => $v->id,
            'sku'        => $v->sku,
            'combination' => $v->combination,
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
        if (isset($data['variants'])) {
            foreach ($data['variants'] as &$variant) {
                $combination = [];

                if (isset($variant['combination_items'])) {
                    foreach ($variant['combination_items'] as $item) {
                        if (!empty($item['option_name']) && !empty($item['option_value'])) {
                            $combination[$item['option_name']] = $item['option_value'];
                        }
                    }
                    unset($variant['combination_items']);
                }

                // Pastikan combination selalu terisi, minimal array kosong
                $variant['combination'] = !empty($combination) ? $combination : (object)[];
            }
            unset($variant);
        }

        return $data;
    }
}
