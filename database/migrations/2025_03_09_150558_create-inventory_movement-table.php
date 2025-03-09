<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Create the table first
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->string('productId');
            $table->string('productName');
            $table->string('uom');
            $table->unsignedBigInteger('qty');
            $table->enum('movementType', ['in', 'out']);
            $table->date('date');
            $table->unsignedBigInteger('pricePerUnit');
            $table->unsignedBigInteger('totalPrice');

            // Foreign keys
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->unsignedBigInteger('purchase_id')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // Step 2: Alter table to add foreign key constraints
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
            $table->foreign('purchase_id')->references('id')->on('purchasings')->onDelete('cascade');
        });

        // Step 3: Convert to utf8mb4 to avoid charset issues (optional)
        DB::statement('ALTER TABLE inventory_movements CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropForeign(['sale_id']);
            $table->dropForeign(['purchase_id']);
        });

        Schema::dropIfExists('inventory_movements');
    }
};
