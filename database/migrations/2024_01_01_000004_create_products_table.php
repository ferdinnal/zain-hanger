<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('image_url')->nullable();
            $table->enum('jenis', [
                'polos',
                'palang_kayu',
                'celana',
                'palang_jepit',
                'celana_palang_jepit',
            ])->nullable();
            $table->enum('kepala', [
                'silver',
                'gold_10',
                'gold_15',
                'gold_20',
                'plat_gold_10',
                'plat_gold_15',
                'plat_silver_10',
            ])->nullable();
            $table->boolean('is_anti_theft')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
