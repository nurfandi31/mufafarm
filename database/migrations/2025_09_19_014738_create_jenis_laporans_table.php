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
        Schema::create('jenis_laporans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('urut');
            $table->string('nama_laporan');
            $table->string('file');
            $table->string('awal_tahun');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_laporans');
    }
};
