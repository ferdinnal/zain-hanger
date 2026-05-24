<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\ProductVariant;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateKombinasi')
                ->label('🔀 Generate Kombinasi')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Generate Kombinasi Variasi')
                ->modalDescription('Semua kombinasi dibuat otomatis dari opsi variasi. Yang sudah ada tidak akan ditimpa.')
                ->modalSubmitActionLabel('Ya, Generate!')
                ->action(function () {
                    $product = $this->record;
                    $product->load('variantOptions.values');

                    $options = $product->variantOptions;

                    if ($options->isEmpty()) {
                        Notification::make()->title('Belum ada opsi variasi!')->warning()->send();
                        return;
                    }

                    foreach ($options as $opt) {
                        if ($opt->values->isEmpty()) {
                            Notification::make()
                                ->title("Opsi \"{$opt->name}\" belum punya nilai!")
                                ->warning()->send();
                            return;
                        }
                    }

                    // Susun cartesian product
                    $combinations = [[]];
                    foreach ($options as $opt) {
                        $newCombinations = [];
                        foreach ($combinations as $existing) {
                            foreach ($opt->values as $val) {
                                $newCombinations[] = array_merge($existing, [
                                    $opt->name => $val->value,
                                ]);
                            }
                        }
                        $combinations = $newCombinations;
                    }

                    $existingVariants = ProductVariant::where('product_id', $product->id)->get();
                    $created = 0;
                    $skipped = 0;

                    foreach ($combinations as $combination) {
                        $exists = $existingVariants->contains(
                            fn($v) => $v->combination == $combination
                        );

                        if ($exists) { $skipped++; continue; }

                        ProductVariant::create([
                            'product_id'  => $product->id,
                            'combination' => $combination,
                            'is_active'   => true,
                            'sort_order'  => 0,
                        ]);
                        $created++;
                    }

                    Notification::make()
                        ->title("✅ {$created} kombinasi dibuat" . ($skipped > 0 ? ", {$skipped} dilewati" : ''))
                        ->success()->send();

                    $this->redirect(request()->header('Referer'));
                }),

            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->record->load([
            'images',
            'variantOptions.values',
            'variants.priceTiers',
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
                'sort_order' => $val->sort_order,
            ])->toArray(),
        ])->toArray();

        $data['variants'] = $record->variants->map(fn($v) => [
            'id'          => $v->id,
            'sku'         => $v->sku,
            'combination' => $v->combination,
            'is_active'   => $v->is_active,
            'sort_order'  => $v->sort_order,
            'priceTiers'  => $v->priceTiers->map(fn($t) => [
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
}
