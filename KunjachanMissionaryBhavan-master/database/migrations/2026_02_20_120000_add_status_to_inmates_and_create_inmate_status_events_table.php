<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inmates', function (Blueprint $table) {
            if (!Schema::hasColumn('inmates', 'status')) {
                $table->string('status', 20)->default('present')->after('admission_number');
                $table->index('status');
            }
        });

        Schema::create('inmate_status_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inmate_id')->constrained('inmates')->cascadeOnDelete();

            $table->string('event_type', 30); // discharge | transfer | deceased | rejoin
            $table->string('from_status', 20)->nullable();
            $table->string('to_status', 20);

            $table->timestamp('effective_at')->useCurrent();
            $table->text('reason')->nullable();
            $table->json('meta')->nullable();
            $table->json('attachments')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['inmate_id', 'created_at']);
            $table->index(['to_status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inmate_status_events');

        Schema::table('inmates', function (Blueprint $table) {
            if (Schema::hasColumn('inmates', 'status')) {
                try { $table->dropIndex(['status']); } catch (\Throwable $e) {}
                $table->dropColumn('status');
            }
        });
    }
};
