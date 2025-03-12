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
        Schema::create('sales_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_id'); // Foreign key to sales
            $table->string('productId');
            $table->string('productName');
            $table->string('uom');
            $table->unsignedBigInteger('qty');
            $table->unsignedBigInteger('pricePerUnit');
            $table->unsignedBigInteger('sellingPricePerUnit');
            $table->unsignedBigInteger('totalSellingPrice');
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraint linking to sales table
            $table->foreign('sales_id')->references('id')->on('sales')->onDelete('cascade');
        });

        Schema::table('sales', function (Blueprint $table) {
            //
            $table->dropColumn('productId');
            $table->dropColumn('productName');
            $table->dropColumn('uom');
            $table->dropColumn('qty');
            $table->dropColumn('pricePerUnit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_items');

        Schema::table('sales', function (Blueprint $table) {
            //
            $table->string('productId');
            $table->string('productName');
            $table->string('uom');
            $table->unsignedBigInteger('qty');
            $table->unsignedBigInteger('pricePerUnit');
        });
    }
};
