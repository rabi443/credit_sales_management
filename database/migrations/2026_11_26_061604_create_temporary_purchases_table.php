<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temporary_purchases', function (Blueprint $table) {
            $table->id();

            // Sequential bill number: 1, 2, 3, 4...
            $table->unsignedBigInteger('purchase_number')->unique();

            $table->string('supplier_name');
            $table->string('invoice_no')->nullable();
            $table->date('purchase_date');

            $table->decimal('total_amount', 12, 2)->default(0);

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temporary_purchases');
    }
};
