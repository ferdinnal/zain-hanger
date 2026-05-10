<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── carts ──────────────────────────────────────────────
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('qty')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();

            // 1 user hanya boleh punya 1 baris per produk
            $table->unique(['user_id', 'product_id']);
        });

        // ── orders ─────────────────────────────────────────────
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone');
            $table->text('shipping_address')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('total_amount', 12, 0);
            $table->enum('status', [
                'pending', 'confirmed', 'processing',
                'shipped', 'done', 'cancelled',
            ])->default('pending');
            $table->enum('source', ['direct', 'cart'])->default('direct');
            $table->timestamp('wa_sent_at')->nullable();
            $table->string('wa_message_preview')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        // ── order_items ────────────────────────────────────────
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->json('product_snapshot');
            $table->integer('qty');
            $table->decimal('price_per_unit', 12, 0);
            $table->decimal('subtotal', 12, 0);
            $table->timestamps();
        });

        // ── notifications (Laravel built-in) ───────────────────
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('carts');
    }
};
