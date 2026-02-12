<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inmate_document_archives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inmate_id')->constrained()->cascadeOnDelete();

            // core | extra
            $table->string('source_type', 20);
            // For core docs: input field key (e.g. aadhaar_card) or column (aadhaar_card_path)
            $table->string('source_key')->nullable();

            // For extra docs
            $table->foreignId('inmate_document_id')->nullable()->constrained('inmate_documents')->nullOnDelete();

            $table->string('document_name')->nullable();
            $table->string('file_path');

            $table->foreignId('archived_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('archived_at')->useCurrent();

            $table->index(['inmate_id', 'source_type']);
            $table->index(['inmate_id', 'source_key']);
            $table->index(['inmate_document_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inmate_document_archives');
    }
};
