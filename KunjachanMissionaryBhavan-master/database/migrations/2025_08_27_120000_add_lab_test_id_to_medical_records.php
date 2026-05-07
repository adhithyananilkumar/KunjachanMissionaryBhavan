<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            if (!Schema::hasColumn('medical_records', 'lab_test_id')) {
                $table->foreignId('lab_test_id')->nullable()->constrained('lab_tests')->nullOnDelete()->after('doctor_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            if (Schema::hasColumn('medical_records', 'lab_test_id')) {
                $table->dropConstrainedForeignId('lab_test_id');
            }
        });
    }
};
