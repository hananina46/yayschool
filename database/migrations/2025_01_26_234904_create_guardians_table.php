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
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade'); // Relasi ke tenant
            $table->string('name'); // Nama orang tua/wali
            $table->string('email')->unique(); // Email
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // Relasi ke user
            $table->string('phone')->nullable(); // Nomor telepon
            $table->string('address')->nullable(); // Alamat
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
