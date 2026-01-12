<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if(!Schema::hasTable('inmate_documents')){
            Schema::create('inmate_documents', function(Blueprint $table){
                $table->id();
                $table->foreignId('inmate_id')->constrained()->cascadeOnDelete();
                $table->string('document_name');
                $table->string('file_path');
                $table->timestamps();
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('inmate_documents');
    }
};
