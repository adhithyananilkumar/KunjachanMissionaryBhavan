<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('doctor_handoffs')) {
            // Ensure from_doctor_id is nullable for SET NULL FK compatibility
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE `doctor_handoffs` MODIFY `from_doctor_id` BIGINT UNSIGNED NULL');
            return;
        }
        Schema::create('doctor_handoffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inmate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('to_doctor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_handoffs');
    }
};
