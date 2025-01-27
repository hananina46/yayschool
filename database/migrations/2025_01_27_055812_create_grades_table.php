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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade'); // Relasi ke tenant
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade'); // Relasi ke siswa
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade'); // Relasi ke mata pelajaran
            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade'); // Relasi ke kelas
            $table->string('type'); // Jenis evaluasi (tugas, ujian, dsb.)
            $table->decimal('score', 5, 2); // Nilai
            $table->text('remarks')->nullable(); // Catatan tambahan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
