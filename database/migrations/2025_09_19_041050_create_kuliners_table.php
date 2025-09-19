<?php

use App\Models\Panen;

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
        Schema::create('kuliners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignIdFor(Panen::class);
            $table->date('tanggal_produksi');
            $table->date('tanggal_kadaluarsa');
            $table->string('nama');
            $table->string('packing');
            $table->string('jumlah');
            $table->string('biaya_produksi');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['ready', 'habis', 'tidak_layak'])->default('ready');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kuliners');
    }
};
