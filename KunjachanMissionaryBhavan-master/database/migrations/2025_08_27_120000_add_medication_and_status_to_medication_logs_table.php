<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('medication_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('medication_logs', 'medication_id')) {
                $table->foreignId('medication_id')->nullable()->after('medical_record_id')->constrained('medications')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('medication_logs', 'status')) {
                $table->string('status', 20)->default('taken')->after('administration_time'); // taken|missed
            }
        });
    }

    public function down(): void
    {
        Schema::table('medication_logs', function (Blueprint $table) {
            if (Schema::hasColumn('medication_logs','medication_id')) {
                // Some MySQL setups require dropping FK index explicitly
                try { $table->dropConstrainedForeignId('medication_id'); } catch (\Throwable $e) { $table->dropForeign(['medication_id']); $table->dropColumn('medication_id'); }
            }
            if (Schema::hasColumn('medication_logs','status')) {
                $table->dropColumn('status');
            }
        });
    }
};
