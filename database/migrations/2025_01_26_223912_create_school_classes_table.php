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
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade'); // Relasi ke tenant
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade'); // Relasi ke tahun ajaran
            $table->string('name'); // Nama kelas (contoh: "Kelas 1A")
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null'); // Wali kelas (nullable)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_classes');
    }
};
