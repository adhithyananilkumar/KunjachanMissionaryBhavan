<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('form')->nullable(); // tablet, capsule, syrup, injection
            $table->string('strength')->nullable(); // e.g., 500 mg
            $table->string('unit')->nullable(); // mg, ml, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
