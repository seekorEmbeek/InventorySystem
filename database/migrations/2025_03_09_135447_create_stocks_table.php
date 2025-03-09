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
        // Create stocks table
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            $table->string('productId');
            $table->string('productName');
            $table->string('uom');
            $table->unsignedBigInteger('totalStock');
            $table->unsignedBigInteger('remainingStock');
            $table->unsignedBigInteger('pricePerUnit');
            $table->timestamps();
        });

        //create sales table
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('buyerName');
            $table->string('productId');
            $table->string('productName');
            $table->string('uom');
            $table->date('date');
            $table->unsignedBigInteger('qty');
            $table->unsignedBigInteger('pricePerUnit');
            $table->unsignedBigInteger('totalPrice');
            $table->unsignedBigInteger('totalPayment');
            $table->unsignedBigInteger('remainingPayment');
            $table->enum('status', ['LUNAS', 'BELUM LUNAS']);
            // Foreign key constraints
            $table->unsignedBigInteger('stock_id')->nullable();
            $table->foreign('stock_id')->references('id')->on('stocks')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');

        Schema::dropIfExists('sales');
    }
};
