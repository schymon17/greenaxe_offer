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
        Schema::table('garden_sections', function (Blueprint $table) {
            $table->string('public_token')->nullable()->unique()->after('canvas_data');
            $table->index('public_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('garden_sections', function (Blueprint $table) {
            $table->dropIndex(['public_token']);
            $table->dropColumn('public_token');
        });
    }
};
