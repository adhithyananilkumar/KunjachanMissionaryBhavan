<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('geriatric_care_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inmate_id')->constrained()->cascadeOnDelete();
            $table->string('mobility_status')->nullable();
            $table->text('dietary_needs')->nullable();
            $table->json('emergency_contact_details')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('geriatric_care_plans'); }
};
