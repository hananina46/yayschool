<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('extracurricular_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('extracurricular_members')->onDelete('cascade'); // Relasi ke member
            $table->string('grade_name'); // Jenis nilai (misal: "Disiplin", "Kehadiran")
            $table->decimal('grade', 5, 2); // Nilai, bisa berupa angka dengan desimal
            $table->text('note')->nullable(); // Catatan tambahan
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('extracurricular_grades');
    }
};
