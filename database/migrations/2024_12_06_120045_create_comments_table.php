<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('comments', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('id_user');
        $table->unsignedBigInteger('id_content');
        $table->string('comment', 255);
        $table->timestamp('date_added')->default(DB::raw('CURRENT_TIMESTAMP'));
        $table->timestamps();

        $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('id_content')->references('id')->on('contents')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
