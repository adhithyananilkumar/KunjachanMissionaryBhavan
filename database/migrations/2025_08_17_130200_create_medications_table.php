<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if(!Schema::hasTable('medications')){
            Schema::create('medications', function(Blueprint $table){
                $table->id();
                $table->foreignId('inmate_id')->constrained()->cascadeOnDelete();
                $table->foreignId('medical_record_id')->nullable()->constrained('medical_records')->nullOnDelete();
                $table->string('name');
                $table->string('dosage')->nullable();
                $table->string('route')->nullable(); // oral, iv, etc
                $table->string('frequency')->nullable(); // e.g., BID, Q6H
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->enum('status',['active','completed','stopped'])->default('active');
                $table->text('instructions')->nullable();
                $table->timestamps();
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
