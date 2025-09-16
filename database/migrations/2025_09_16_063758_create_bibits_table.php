<?php

use App\Models\Kolam;
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
        Schema::create('bibits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignIdFor(Kolam::class);
            $table->string('nama');
            $table->string('jenis');
            $table->date('tanggal_datang');
            $table->string('jumlah');
            $table->string('sumber');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bibits');
    }
};
