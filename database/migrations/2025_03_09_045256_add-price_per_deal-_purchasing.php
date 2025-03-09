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
        Schema::table('purchasings', function (Blueprint $table) {
            $table->decimal('pricePerUnit');
            $table->decimal('smallPrice')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('purchasings', function (Blueprint $table) {
            $table->drop('pricePerUnit');
            $table->integer('smallPrice')->change();
        });
    }
};
