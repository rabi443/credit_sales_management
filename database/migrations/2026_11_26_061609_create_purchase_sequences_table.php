<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_sequences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('last_number')->default(0);
            $table->timestamps();
        });

        // Start numbering after the existing permanent purchases.
        $lastNumber = DB::table('purchases')
            ->max('purchase_number') ?? 0;

        DB::table('purchase_sequences')->insert([
            'id' => 1,
            'last_number' => $lastNumber,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_sequences');
    }
};
