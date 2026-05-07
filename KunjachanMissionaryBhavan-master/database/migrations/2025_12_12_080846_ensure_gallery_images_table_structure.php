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
        if (!Schema::hasTable('gallery_images')) {
            Schema::create('gallery_images', function (Blueprint $table) {
                $table->id();
                $table->string('image_path');
                $table->string('caption')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('gallery_images', function (Blueprint $table) {
                if (!Schema::hasColumn('gallery_images', 'image_path')) {
                    $table->string('image_path');
                }
                if (!Schema::hasColumn('gallery_images', 'caption')) {
                    $table->string('caption')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
