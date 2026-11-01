<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('credit_payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credit_payment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('credit_sale_id')->constrained()->restrictOnDelete();
            $table->decimal('amount', 12, 2);
            $table->timestamps();
            $table->unique(['credit_payment_id', 'credit_sale_id'],'payment_sale_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_payment_allocations');
    }
};
