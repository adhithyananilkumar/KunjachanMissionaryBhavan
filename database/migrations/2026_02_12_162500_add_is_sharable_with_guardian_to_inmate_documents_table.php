<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('inmate_documents')) {
            return;
        }

        Schema::table('inmate_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('inmate_documents', 'is_sharable_with_guardian')) {
                $table->boolean('is_sharable_with_guardian')
                    ->default(false)
                    ->after('file_path');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('inmate_documents')) {
            return;
        }

        Schema::table('inmate_documents', function (Blueprint $table) {
            if (Schema::hasColumn('inmate_documents', 'is_sharable_with_guardian')) {
                $table->dropColumn('is_sharable_with_guardian');
            }
        });
    }
};
