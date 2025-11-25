<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inmates', function(Blueprint $table){
            $table->text('critical_alert')->nullable()->after('notes');
        });
    }
    public function down(): void
    {
        Schema::table('inmates', function(Blueprint $table){
            $table->dropColumn('critical_alert');
        });
    }
};
