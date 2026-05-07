<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mental_health_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inmate_id')->constrained()->cascadeOnDelete();
            $table->string('diagnosis')->nullable();
            $table->string('therapy_frequency')->nullable();
            $table->text('current_meds')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('mental_health_plans');
    }
};
