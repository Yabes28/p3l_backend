<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            // Menambahkan foreign key ke pembelis (cart holder)
            $table->foreignId('cart_holder_user_id')
                  ->nullable()
                  ->after('status')
                  ->constrained('pembelis', 'pembeliID') // pastikan 'pembelis' punya kolom 'pembeliID'
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropForeign(['cart_holder_user_id']);
            $table->dropColumn('cart_holder_user_id');
        });
    }
};
