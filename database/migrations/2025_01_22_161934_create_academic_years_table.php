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
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade'); // Relasi ke tabel tenants
            $table->string('name'); // Contoh: "Tahun Ajaran 2025/2026"
            $table->date('start_date'); // Tanggal mulai tahun ajaran
            $table->date('end_date'); // Tanggal akhir tahun ajaran
            $table->boolean('is_active')->default(false); // Tahun ajaran aktif atau tidak
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
