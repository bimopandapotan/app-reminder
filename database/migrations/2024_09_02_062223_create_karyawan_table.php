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
    Schema::create('karyawan', function (Blueprint $table) {
        $table->id();
        $table->string('nama');
        $table->string('nik')->unique();
        $table->enum('jenis_kelamin', ['L', 'P']);
        $table->date('tgl_masuk');
        $table->date('tgl_lahir');
        $table->string('tempat_lahir');
        $table->string('no_hp');
        $table->string('agama');
        $table->string('divisi');
        $table->string('jabatan');
        $table->text('alamat');
        $table->boolean('status_cuti')->default(0); 
        $table->enum('status_karyawan', ['aktif', 'tidak-aktif']);
        $table->enum('size_baju', ['xs','s','m','l','xl','xxl','xxxl']);
        $table->string('foto_karyawan')->nullable();
        $table->string('foto_ktp')->nullable();
        $table->enum('bpjs_ketenagakerjaan', ['Ada','Tidak Ada']);
        $table->enum('bpjs_kesehatan', ['Ada','Tidak Ada']);
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('karyawan');
}

};