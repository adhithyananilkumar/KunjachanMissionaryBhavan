<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('guardian_messages', function (Blueprint $table) {
            $table->id();
            // Assuming guardians table exists and uses bigIncrements
            $table->foreignId('guardian_id')->constrained()->cascadeOnDelete();
            $table->text('message_text');
            $table->boolean('sent_by_guardian')->default(true);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('guardian_messages');
    }
};
