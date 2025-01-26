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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade'); // Relasi ke tenant
            $table->foreignId('class_id')->nullable()->constrained('school_classes')->onDelete('set null'); // Relasi ke kelas
            $table->string('name'); // Nama siswa
            $table->string('email')->unique(); // Email siswa
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // Relasi ke user
            $table->string('nisn')->nullable()->unique(); // Nomor Induk Siswa Nasional
            $table->date('dob')->nullable(); // Tanggal lahir
            $table->string('gender')->nullable(); // Jenis kelamin (L/P)
            $table->string('phone')->nullable(); // Nomor telepon
            $table->string('address')->nullable(); // Alamat
            $table->string('profile_photo')->nullable(); // Foto profil
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
