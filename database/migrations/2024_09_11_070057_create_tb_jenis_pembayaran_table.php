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
    Schema::create('tb_jenis_pembayaran', function (Blueprint $table) {
        $table->id();
        $table->string('jenis_pembayaran');
        $table->enum('status', ['aktif', 'tidak-aktif']);
        $table->date('tanggal_jatuh_tempo');
        $table->unsignedBigInteger('telepon_id');
        $table->timestamps();

        $table->foreign('telepon_id')->references('id')->on('telepons')->onDelete('cascade');
    });
}



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_jenis_pembayaran');
    }
};