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
    public function up()
    {
        Schema::create('tb_reminder', function (Blueprint $table) {
            $table->id();
            $table->string('tentang_reminder');
            $table->text('keterangan')->nullable();
            $table->date('tanggal_reminder');
            $table->enum('status', ['aktif', 'tidak-aktif']);
            $table->enum('status_pelaksanaan', ['sudah', 'belum']);
            $table->unsignedBigInteger('telepon_id');
            $table->timestamps();

            $table->foreign('telepon_id')->references('id')->on('telepons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_reminder');
    }
};
