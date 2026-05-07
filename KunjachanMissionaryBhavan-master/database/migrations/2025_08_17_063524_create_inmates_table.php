<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('inmates', function (Blueprint $table) {
        $table->id();
        $table->string('full_name');
        $table->date('date_of_birth');
        $table->string('gender');
        $table->date('admission_date');
        $table->foreignId('institution_id')->constrained()->onDelete('cascade');
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inmates');
    }
};
