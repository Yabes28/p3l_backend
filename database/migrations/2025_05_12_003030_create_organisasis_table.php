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
        Schema::create('organisasis', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->id('organisasiID');
            $table->string('email')->unique();
            $table->string('kontak');
            $table->string('alamat');
            $table->string('namaOrganisasi');
            $table->string('role')->default('organisasi');
            $table->string('password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisasis');
    }
};
