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
        Schema::table('sales', function (Blueprint $table) {
            //
            //drop foreignt key
            $table->dropForeign(['stock_id']);
            //drop column
            $table->dropColumn('stock_id');
        });

        Schema::table('sales_items', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('stock_id')->after('sales_id');
            $table->foreign('stock_id')->references('id')->on('stocks')->onDelete('cascade');
        });

        // drop foreignt key in inventory_movements
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropForeign(['sale_id']);
            $table->dropColumn('sale_id');
        });

        //add foreignt key in inventory_movements
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->unsignedBigInteger('salesItem_id')->nullable()->after('purchase_id');
            $table->foreign('salesItem_id')->references('id')->on('sales_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('stock_id');
            $table->foreign('stock_id')->references('id')->on('stocks')->onDelete('cascade');
        });

        Schema::table('sales_items', function (Blueprint $table) {
            //
            $table->dropForeign(['stock_id']);
            $table->dropColumn('stock_id');
        });

        Schema::table('inventory_movements', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('stock_id')->nullable()->after('id');
            $table->foreign('stock_id')->references('id')->on('stocks')->onDelete('cascade');
        });

        Schema::table('inventory_movements', function (Blueprint $table) {
            //
            $table->dropForeign(['salesItem_id']);
            $table->dropColumn('salesItem_id');
        });
    }
};
