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
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->string('foto_masuk')->nullable();
            $table->string('lokasi_masuk')->nullable();
            $table->time('jam_keluar')->nullable();
            $table->string('foto_keluar')->nullable();
            $table->string('lokasi_keluar')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Unique constraint untuk memastikan siswa hanya bisa absen sekali per hari
            $table->unique(['siswa_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};