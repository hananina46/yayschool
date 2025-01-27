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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade'); // Relasi ke tenant
            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade'); // Relasi ke kelas
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade'); // Relasi ke mata pelajaran
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade'); // Relasi ke guru
            $table->string('day'); // Hari (contoh: Senin, Selasa)
            $table->time('start_time'); // Waktu mulai
            $table->time('end_time'); // Waktu selesai
            $table->text('description')->nullable(); // Deskripsi opsional
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
