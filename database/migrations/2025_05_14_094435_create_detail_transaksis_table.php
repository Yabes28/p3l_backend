<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('detail_transaksis', function (Blueprint $table) {
            $table->bigIncrements('id_detail_transaksi');
            $table->unsignedBigInteger('transaksiID'); // FK ke transaksi
            $table->unsignedBigInteger('idProduk');    // FK ke produk
            $table->integer('jumlah');                // jumlah produk yang dibeli
            $table->float('harga_satuan');            // harga per item saat dibeli
            $table->timestamps();

            $table->foreign('transaksiID')->references('idTransaksi')->on('transaksi')->onDelete('cascade');
            $table->foreign('produkID')->references('id')->on('produk')->onDelete('cascade'); // sesuaikan nama dan PK di tabel produk
        });
    }

    public function down()
    {
        Schema::dropIfExists('detail_transaksis');
    }
};
