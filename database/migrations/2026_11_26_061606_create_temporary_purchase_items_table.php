<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temporary_purchase_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('temporary_purchase_id')
                ->constrained('temporary_purchases')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();

            $table->string('product_name');

            $table->decimal('quantity', 12, 2);
            $table->string('unit', 30)->default('pcs');

            $table->decimal('pieces_per_pack', 12, 2)->default(1);
            $table->decimal('stock_quantity', 12, 2)->default(0);

            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_price', 12, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temporary_purchase_items');
    }
};
