<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('bug_reports') && !Schema::hasTable('support_tickets')) {
            Schema::rename('bug_reports', 'support_tickets');
        }
        if(Schema::hasTable('support_tickets')) {
            Schema::table('support_tickets', function(Blueprint $table){
                if(!Schema::hasColumn('support_tickets','last_activity_at')){
                    $table->timestamp('last_activity_at')->nullable()->after('updated_at');
                }
            });
        }
        if(!Schema::hasTable('ticket_replies')) {
            Schema::create('ticket_replies', function(Blueprint $table){
                $table->id();
                $table->foreignId('support_ticket_id')->constrained('support_tickets')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->text('message');
                $table->string('attachment_path')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_replies');
        if (Schema::hasTable('support_tickets')) {
            Schema::rename('support_tickets', 'bug_reports');
            Schema::table('bug_reports', function(Blueprint $table){
                if(Schema::hasColumn('bug_reports','last_activity_at')){
                    $table->dropColumn('last_activity_at');
                }
            });
        }
    }
};
