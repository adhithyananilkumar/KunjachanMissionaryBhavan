<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            if (Schema::hasColumn('locations', 'name')) {
                $table->dropColumn('name');
            }
            $table->foreignId('block_id')->after('institution_id')->nullable()->constrained('blocks')->nullOnDelete();
            $table->string('number')->after('type');
            $table->index(['block_id','type','number']);
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropIndex(['block_id','type','number']);
            $table->dropForeign(['block_id']);
            $table->dropColumn(['block_id','number']);
            $table->string('name')->after('institution_id');
        });
    }
};
