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
        //
        Schema::create('stock_conversion_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stockId');
            $table->unsignedBigInteger('productId');
            $table->string('productName');
            $table->string('uomBefore');
            $table->unsignedBigInteger('qtyBefore');
            $table->unsignedBigInteger('pricePerUnitBefore');
            $table->string('uomAfter');
            $table->unsignedBigInteger('qtyAfter');
            $table->unsignedBigInteger('pricePerUnitAfter');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('stock_conversion_histories', function (Blueprint $table) {
            $table->foreign('stockId')->references('id')->on('stocks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('stock_conversion_histories', function (Blueprint $table) {
            $table->dropForeign(['stockId']);
        });

        Schema::dropIfExists('stock_conversion_histories');
    }
};
