<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inmate_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inmate_id')->constrained()->onDelete('cascade');
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('INR');
            $table->date('payment_date');
            $table->string('period_label')->nullable(); // e.g. "Nov 2025", "2025-Q4"
            $table->enum('status', ['pending','paid','failed','refunded'])->default('paid');
            $table->string('method')->nullable(); // cash, bank_transfer, upi, card, etc.
            $table->string('reference')->nullable(); // receipt/transaction id
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['institution_id','payment_date']);
            $table->index(['inmate_id','payment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inmate_payments');
    }
};
