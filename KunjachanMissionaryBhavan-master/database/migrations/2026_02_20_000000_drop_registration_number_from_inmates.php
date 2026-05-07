<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inmates', function (Blueprint $table) {
            if (Schema::hasColumn('inmates', 'registration_number')) {
                $table->dropColumn('registration_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inmates', function (Blueprint $table) {
            if (!Schema::hasColumn('inmates', 'registration_number')) {
                $table->string('registration_number')->nullable()->after('admission_number');
            }
        });
    }
};
