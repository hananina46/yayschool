<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('extracurricular_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade'); // Tenant wajib
            $table->foreignId('extracurricular_id')->constrained('extracurricular_names')->onDelete('cascade'); // Ekstrakurikuler
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade'); // Student dari tabel students
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('extracurricular_members');
    }
};
