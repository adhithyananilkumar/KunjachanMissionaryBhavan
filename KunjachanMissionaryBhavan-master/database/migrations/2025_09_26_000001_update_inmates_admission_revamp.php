<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inmates', function (Blueprint $table) {
            // Core identifiers
            if (!Schema::hasColumn('inmates', 'admission_number')) {
                $table->string('admission_number', 32)->nullable()->after('id');
            }

            // Admission / relations
            if (!Schema::hasColumn('inmates', 'admitted_by')) {
                $table->foreignId('admitted_by')->nullable()->after('institution_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('inmates', 'verified_by')) {
                $table->foreignId('verified_by')->nullable()->after('admitted_by')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('inmates', 'consent_signed_at')) {
                $table->timestamp('consent_signed_at')->nullable()->after('admission_date');
            }
            if (!Schema::hasColumn('inmates', 'room_location_id')) {
                $table->foreignId('room_location_id')->nullable()->after('institution_id')->constrained('locations')->nullOnDelete();
            }

            // Personal details
            if (!Schema::hasColumn('inmates', 'age')) { $table->unsignedSmallInteger('age')->nullable()->after('date_of_birth'); }
            if (!Schema::hasColumn('inmates', 'marital_status')) { $table->string('marital_status', 50)->nullable()->after('gender'); }
            if (!Schema::hasColumn('inmates', 'blood_group')) { $table->string('blood_group', 10)->nullable()->after('marital_status'); }
            if (!Schema::hasColumn('inmates', 'height')) { $table->decimal('height', 5, 2)->nullable()->after('blood_group'); }
            if (!Schema::hasColumn('inmates', 'weight')) { $table->decimal('weight', 5, 2)->nullable()->after('height'); }
            if (!Schema::hasColumn('inmates', 'identification_marks')) { $table->text('identification_marks')->nullable()->after('weight'); }
            if (!Schema::hasColumn('inmates', 'religion')) { $table->string('religion', 100)->nullable()->after('identification_marks'); }
            if (!Schema::hasColumn('inmates', 'caste')) { $table->string('caste', 100)->nullable()->after('religion'); }
            if (!Schema::hasColumn('inmates', 'nationality')) { $table->string('nationality', 100)->nullable()->after('caste'); }
            if (!Schema::hasColumn('inmates', 'address')) { $table->json('address')->nullable()->after('nationality'); }
            if (!Schema::hasColumn('inmates', 'father_name')) { $table->string('father_name')->nullable()->after('last_name'); }
            if (!Schema::hasColumn('inmates', 'mother_name')) { $table->string('mother_name')->nullable()->after('father_name'); }
            if (!Schema::hasColumn('inmates', 'spouse_name')) { $table->string('spouse_name')->nullable()->after('mother_name'); }
            if (!Schema::hasColumn('inmates', 'guardian_name')) { $table->string('guardian_name')->nullable()->after('spouse_name'); }

            // Education / health / docs
            if (!Schema::hasColumn('inmates', 'education_details')) { $table->json('education_details')->nullable()->after('guardian_address'); }
            if (!Schema::hasColumn('inmates', 'documents')) { $table->json('documents')->nullable()->after('education_details'); }
            if (!Schema::hasColumn('inmates', 'case_notes')) { $table->longText('case_notes')->nullable()->after('notes'); }
            if (!Schema::hasColumn('inmates', 'health_info')) { $table->json('health_info')->nullable()->after('case_notes'); }
            if (!Schema::hasColumn('inmates', 'created_by')) { $table->foreignId('created_by')->nullable()->after('updated_at')->constrained('users')->nullOnDelete(); }
            if (!Schema::hasColumn('inmates', 'updated_by')) { $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete(); }

            // Soft deletes
            if (!Schema::hasColumn('inmates', 'deleted_at')) { $table->softDeletes(); }
        });

        // Indices and uniqueness
        Schema::table('inmates', function (Blueprint $table) {
            try { $table->unique('admission_number'); } catch (\Throwable $e) { /* may already exist */ }
            try { $table->index('admission_date'); } catch (\Throwable $e) { /* ignore */ }
            try { $table->index('institution_id'); } catch (\Throwable $e) { /* ignore */ }
        });
    }

    public function down(): void
    {
        Schema::table('inmates', function (Blueprint $table) {
            // Drop indices first (wrapped in try to be safe)
            try { $table->dropUnique(['admission_number']); } catch (\Throwable $e) {}
            try { $table->dropIndex(['admission_date']); } catch (\Throwable $e) {}
            // Keep many columns for data safety on rollback; only drop those that are safe and newly introduced
            $dropCols = [
                'admission_number','admitted_by','verified_by','consent_signed_at','room_location_id',
                'marital_status','blood_group','height','weight','identification_marks','religion','caste','nationality','address',
                'father_name','mother_name','spouse_name','guardian_name','education_details','documents','case_notes','health_info',
                'created_by','updated_by'
            ];
            foreach ($dropCols as $col) {
                if (Schema::hasColumn('inmates', $col)) {
                    if (in_array($col, ['admitted_by','verified_by','room_location_id','created_by','updated_by'])) {
                        try { $table->dropConstrainedForeignId($col); } catch (\Throwable $e) { try { $table->dropForeign([$col]); } catch (\Throwable $e2) {} $table->dropColumn($col); }
                    } else {
                        $table->dropColumn($col);
                    }
                }
            }
            if (Schema::hasColumn('inmates','deleted_at')) { $table->dropSoftDeletes(); }
        });
    }
};
