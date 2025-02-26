<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assigned_bills', function (Blueprint $table) {
            $table->enum('payment_method', ['VA', 'manual_transfer', 'gift_card', 'credit_card'])->nullable()->after('status');
            $table->string('payment_url')->nullable()->after('payment_method');
            $table->string('payment_proof')->nullable()->after('payment_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assigned_bills', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_url', 'payment_proof']);
        });
    }
};
