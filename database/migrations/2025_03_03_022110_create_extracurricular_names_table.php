<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('extracurricular_names', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade'); // Tenant Wajib
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('supervisor_id')->constrained('users')->onDelete('cascade'); // Guru/Pengawas
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('extracurricular_names');
    }
};
