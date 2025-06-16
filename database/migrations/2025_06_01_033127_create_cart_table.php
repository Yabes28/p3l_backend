<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('cart', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id');
    $table->unsignedBigInteger('product_id');
    $table->decimal('price_at_add', 15, 2);
    $table->timestamps();

    $table->foreign('user_id')->references('pembeliID')->on('pembelis')->onDelete('cascade');
    $table->foreign('product_id')->references('idProduk')->on('barangs')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('cart');
    }
};