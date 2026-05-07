<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('counseling_progress_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inmate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->dateTime('note_date');
            $table->text('progress_assessment');
            $table->text('milestones_achieved')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('counseling_progress_notes'); }
};
