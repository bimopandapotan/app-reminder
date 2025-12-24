<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainTable extends Migration
{
    public function up()
    {
        Schema::create('domain', function (Blueprint $table) {
            $table->id();
            $table->string('nama_domain')->unique();
            $table->string('nama_perusahaan');
            $table->date('tgl_expired');
            $table->string('telepon');
            $table->decimal('nominal', 10, 2);
            $table->enum('status_berlangganan', ['Aktif', 'Tidak Aktif']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('domain');
    }
}