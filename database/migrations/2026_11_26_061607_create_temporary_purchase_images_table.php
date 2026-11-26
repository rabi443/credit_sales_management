<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temporary_purchase_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('temporary_purchase_id')
                ->constrained('temporary_purchases')
                ->cascadeOnDelete();

            $table->string('image_path');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temporary_purchase_images');
    }
};
