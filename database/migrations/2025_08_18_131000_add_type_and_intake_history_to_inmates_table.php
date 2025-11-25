<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('inmates', function (Blueprint $table) {
            $table->string('type')->nullable()->after('institution_id');
            $table->text('intake_history')->nullable()->after('notes');
        });
    }
    public function down(): void {
        Schema::table('inmates', function (Blueprint $table) {
            $table->dropColumn(['type','intake_history']);
        });
    }
};
