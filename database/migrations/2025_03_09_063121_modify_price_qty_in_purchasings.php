<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchasings', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('purchasePrice')->change();
            $table->unsignedBigInteger('purchaseQty')->change();
            $table->unsignedBigInteger('smallPrice')->change(); // Optional: Also change smallPrice if needed
            $table->unsignedBigInteger('smallQty')->change(); // Optional: Change smallQty as well
            $table->unsignedBigInteger('pricePerUnit')->change(); // Optional: Change smallQty as well
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchasings', function (Blueprint $table) {
            //
            $table->decimal('purchasePrice')->change();
            $table->decimal('purchaseQty')->change();
            $table->decimal('smallPrice')->change(); // Optional: Revert smallPrice
            $table->decimal('smallQty')->change(); // Optional: Revert smallQty
            $table->decimal('pricePerUnit')->change(); // Optional: Revert smallQty
        });
    }
};
