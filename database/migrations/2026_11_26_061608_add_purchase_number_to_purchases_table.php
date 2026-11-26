<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_number')
                ->nullable()
                ->after('id');
        });

        // Give existing purchase records sequential numbers.
        $purchases = DB::table('purchases')
            ->orderBy('id')
            ->get(['id']);

        $number = 1;

        foreach ($purchases as $purchase) {
            DB::table('purchases')
                ->where('id', $purchase->id)
                ->update(['purchase_number' => $number]);

            $number++;
        }

        Schema::table('purchases', function (Blueprint $table) {
            $table->unique('purchase_number');
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropUnique(['purchase_number']);
            $table->dropColumn('purchase_number');
        });
    }
};
