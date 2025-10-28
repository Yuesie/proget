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
        Schema::create('role_user', function (Blueprint $table) {
           $table->id(); // Membuat kolom 'id' sebagai primary key
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['SECURITY', 'HSSE', 'TEKNIK', 'ADMIN']); 
            $table->timestamps();
            
            // Opsional: Pastikan kombinasi user_id dan role unik
            $table->unique(['user_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_user');
    }
};
