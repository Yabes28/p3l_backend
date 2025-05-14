<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->bigIncrements('idTransaksi');
            $table->unsignedBigInteger('pembeliID'); // FK ke tabel pembeli
            $table->float('totalHarga');
            $table->string('status');
            $table->string('tanggalTransaksi');
            $table->string('metodePengiriman');
            $table->float('biayaPengiriman')->nullable();
            $table->float('diskon')->nullable();
            $table->timestamps();

            $table->foreign('pembeliID')->references('pembeliID')->on('pembeli')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksis');
    }
};
