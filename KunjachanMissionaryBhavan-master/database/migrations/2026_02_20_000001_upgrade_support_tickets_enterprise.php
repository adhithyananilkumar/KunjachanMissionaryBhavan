<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('support_tickets')) {
            Schema::table('support_tickets', function (Blueprint $table) {
                if (!Schema::hasColumn('support_tickets', 'public_id')) {
                    $table->string('public_id', 40)->nullable()->unique()->after('id');
                }
                if (!Schema::hasColumn('support_tickets', 'module')) {
                    $table->string('module', 80)->nullable()->after('title');
                }
                if (!Schema::hasColumn('support_tickets', 'severity')) {
                    $table->string('severity', 20)->nullable()->after('module');
                }
                if (!Schema::hasColumn('support_tickets', 'environment')) {
                    $table->json('environment')->nullable()->after('severity');
                }
                if (!Schema::hasColumn('support_tickets', 'page_url')) {
                    $table->text('page_url')->nullable()->after('environment');
                }
                if (!Schema::hasColumn('support_tickets', 'app_version')) {
                    $table->string('app_version', 50)->nullable()->after('page_url');
                }
                if (!Schema::hasColumn('support_tickets', 'deployment_tag')) {
                    $table->string('deployment_tag', 80)->nullable()->after('app_version');
                }
                if (!Schema::hasColumn('support_tickets', 'fixed_in_version')) {
                    $table->string('fixed_in_version', 50)->nullable()->after('deployment_tag');
                }
                if (!Schema::hasColumn('support_tickets', 'assigned_to')) {
                    $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete()->after('user_id');
                }
                if (!Schema::hasColumn('support_tickets', 'resolved_at')) {
                    $table->timestamp('resolved_at')->nullable()->after('last_activity_at');
                }
                if (!Schema::hasColumn('support_tickets', 'resolution_summary')) {
                    $table->text('resolution_summary')->nullable()->after('resolved_at');
                }
                if (!Schema::hasColumn('support_tickets', 'close_reason')) {
                    $table->string('close_reason', 80)->nullable()->after('resolution_summary');
                }
                if (!Schema::hasColumn('support_tickets', 'closed_at')) {
                    $table->timestamp('closed_at')->nullable()->after('close_reason');
                }
                if (!Schema::hasColumn('support_tickets', 'archived_at')) {
                    $table->timestamp('archived_at')->nullable()->after('closed_at');
                }
                if (!Schema::hasColumn('support_tickets', 'user_last_seen_at')) {
                    $table->timestamp('user_last_seen_at')->nullable()->after('archived_at');
                }
                if (!Schema::hasColumn('support_tickets', 'developer_last_seen_at')) {
                    $table->timestamp('developer_last_seen_at')->nullable()->after('user_last_seen_at');
                }
                if (!Schema::hasColumn('support_tickets', 'screenshot_paths')) {
                    $table->json('screenshot_paths')->nullable()->after('screenshot_path');
                }

                if (Schema::hasColumn('support_tickets', 'status')) {
                    $table->index('status');
                }
                if (Schema::hasColumn('support_tickets', 'severity')) {
                    $table->index('severity');
                }
                if (Schema::hasColumn('support_tickets', 'module')) {
                    $table->index('module');
                }
                if (Schema::hasColumn('support_tickets', 'assigned_to')) {
                    $table->index('assigned_to');
                }
            });

            // Backfill public ids for existing records (avoid breaking route model binding)
            $tickets = DB::table('support_tickets')->select('id', 'public_id')->whereNull('public_id')->orderBy('id')->get();
            foreach ($tickets as $t) {
                DB::table('support_tickets')
                    ->where('id', $t->id)
                    ->update(['public_id' => 'TKT-' . (string) Str::ulid()]);
            }
        }

        if (Schema::hasTable('ticket_replies')) {
            Schema::table('ticket_replies', function (Blueprint $table) {
                if (!Schema::hasColumn('ticket_replies', 'attachments')) {
                    $table->json('attachments')->nullable()->after('attachment_path');
                }
            });
        }

        if (!Schema::hasTable('support_ticket_activities')) {
            Schema::create('support_ticket_activities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('support_ticket_id')->constrained('support_tickets')->onDelete('cascade');
                $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('type', 50);
                $table->json('meta')->nullable();
                $table->timestamps();
                $table->index(['support_ticket_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('support_ticket_activities')) {
            Schema::dropIfExists('support_ticket_activities');
        }

        if (Schema::hasTable('ticket_replies')) {
            Schema::table('ticket_replies', function (Blueprint $table) {
                if (Schema::hasColumn('ticket_replies', 'attachments')) {
                    $table->dropColumn('attachments');
                }
            });
        }

        if (Schema::hasTable('support_tickets')) {
            Schema::table('support_tickets', function (Blueprint $table) {
                if (Schema::hasColumn('support_tickets', 'assigned_to')) {
                    try {
                        $table->dropForeign(['assigned_to']);
                    } catch (\Throwable $e) {
                        // ignore
                    }
                }
                foreach ([
                    'public_id', 'module', 'severity', 'environment', 'page_url', 'app_version',
                    'deployment_tag', 'fixed_in_version', 'assigned_to', 'resolved_at',
                    'resolution_summary', 'close_reason', 'closed_at', 'archived_at',
                    'user_last_seen_at', 'developer_last_seen_at', 'screenshot_paths',
                ] as $col) {
                    if (Schema::hasColumn('support_tickets', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
