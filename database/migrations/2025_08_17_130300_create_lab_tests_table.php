<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if(!Schema::hasTable('lab_tests')){
            Schema::create('lab_tests', function(Blueprint $table){
                $table->id();
                $table->foreignId('inmate_id')->constrained()->cascadeOnDelete();
                $table->foreignId('ordered_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('test_name');
                $table->enum('status',['ordered','in_progress','completed','cancelled'])->default('ordered');
                $table->date('ordered_date')->nullable();
                $table->date('completed_date')->nullable();
                $table->text('notes')->nullable();
                $table->text('result_notes')->nullable();
                $table->string('result_file_path')->nullable();
                $table->timestamps();
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('lab_tests');
    }
};
