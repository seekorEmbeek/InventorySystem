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
        Schema::create('purchasings', function (Blueprint $table) {
            $table->id();
            $table->string('supplierName');
            $table->string('productId');
            $table->string('productName');
            $table->date('date');
            $table->decimal('purchaseQty');
            $table->string('purchaseUom');
            $table->decimal('purchasePrice');
            $table->string('purchaseStatus');
            $table->decimal('smallQty');
            $table->string('smallUom');
            $table->integer('smallPrice');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchasings');
    }
};
