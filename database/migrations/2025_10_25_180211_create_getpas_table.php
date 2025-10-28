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
        Schema::create('getpas', function (Blueprint $table) {
            $table->id();
    // Relasi ke User yang mengajukan
    $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
    
    // Data Getpas
    $table->string('nomor_getpas')->unique();
    $table->string('pekerjaan');
    $table->string('perihal');
    $table->string('fungsi')->nullable(); // Fungsi Pemohon
    $table->json('data_barang'); // Menyimpan array barang/alat kerja sebagai JSON
    
    // Status
    $table->enum('status', ['DRAFT', 'PENDING', 'REJECTED', 'APPROVED_FINAL'])->default('DRAFT');
    
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('getpas');
    }
};
