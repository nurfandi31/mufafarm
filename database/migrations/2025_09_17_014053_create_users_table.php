<?php

use App\Models\Level;
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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama_lengkap');
            $table->string('nama_panggilan');
            $table->foreignIdFor(Level::class);
            $table->string('nik');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P'])->default('L');
            $table->text('alamat');
            $table->string('telpon');
            $table->string('id_sidik_jari');
            $table->date('tanggal_masuk');
            $table->string('gaji');
            $table->string('status');
            $table->string('foto')->nullable();
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
