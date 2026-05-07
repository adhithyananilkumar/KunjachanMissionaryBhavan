<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type'); // room | bed | cell
            $table->integer('capacity')->default(1);
            $table->string('status')->default('available');
            $table->timestamps();
            $table->index(['institution_id','type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
