<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if(!Schema::hasColumn('users','bug_report_enabled')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('bug_report_enabled')->default(false)->after('can_report_bugs');
            });
        }
    }

    public function down(): void
    {
        if(Schema::hasColumn('users','bug_report_enabled')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('bug_report_enabled');
            });
        }
    }
};
