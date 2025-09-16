<?php

use App\Models\Bibit;
use App\Models\Pakan;
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
        Schema::create('pemberian_pakans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignIdFor(Bibit::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Pakan::class)->constrained()->cascadeOnDelete();
            $table->date('tanggal_pemberian');
            $table->string('jumlah');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemberian_pakans');
    }
};
