<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bug_reports', function (Blueprint $table) {
            $table->string('title')->after('user_id')->default('');
            $table->string('developer_attachment_path')->nullable()->after('developer_reply');
        });
    }
    public function down(): void
    {
        Schema::table('bug_reports', function (Blueprint $table) {
            $table->dropColumn(['title','developer_attachment_path']);
        });
    }
};
