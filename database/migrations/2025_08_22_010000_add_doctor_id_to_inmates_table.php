<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inmates', function (Blueprint $table) {
            $table->foreignId('doctor_id')->nullable()->after('institution_id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inmates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('doctor_id');
        });
    }
};
