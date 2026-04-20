<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('section_elements', function (Blueprint $table) {
            $table->string('zone_ref')->nullable()->after('garden_section_id');
            $table->string('zone_label')->nullable()->after('zone_ref');
        });
    }

    public function down(): void
    {
        Schema::table('section_elements', function (Blueprint $table) {
            $table->dropColumn(['zone_ref', 'zone_label']);
        });
    }
};
