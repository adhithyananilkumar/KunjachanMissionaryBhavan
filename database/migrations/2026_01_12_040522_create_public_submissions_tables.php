<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contact_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('message');
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });

        Schema::create('donation_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('donor_name');
            $table->string('donor_email')->nullable();
            $table->string('donor_phone')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->default('pending'); // pending, contacted, completed, cancelled
            $table->json('details')->nullable(); // For meal type (breakfast/lunch/etc) or other metadata
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donation_requests');
        Schema::dropIfExists('contact_submissions');
    }
};
