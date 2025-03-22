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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade'); // Relasi ke tenant
            $table->foreignId('user_announcer')->constrained('users')->onDelete('cascade'); // Pengumuman dibuat oleh siapa
            $table->enum('users_announced', ['all', 'teachers', 'students', 'parents', 'others']); // Penerima pengumuman
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // Jika `others`, spesifik user
            $table->text('message'); // Isi pengumuman
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
