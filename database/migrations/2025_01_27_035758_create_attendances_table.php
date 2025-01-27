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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade'); // Relasi ke tenant
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade'); // Relasi ke jadwal
            $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('cascade'); // Relasi ke siswa
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('cascade'); // Relasi ke guru
            $table->date('date'); // Tanggal kehadiran
            $table->enum('status', ['present', 'absent', 'sick', 'permission'])->default('present'); // Status kehadiran
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
