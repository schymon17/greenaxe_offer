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
        Schema::table('garden_projects', function (Blueprint $table) {
            $table->string('street')->nullable()->after('name');
            $table->string('city')->nullable()->after('street');
            $table->string('postal_code')->nullable()->after('city');
            $table->dropColumn(['location', 'style']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('garden_projects', function (Blueprint $table) {
            $table->string('location')->nullable();
            $table->string('style')->nullable();
            $table->dropColumn(['street', 'city', 'postal_code']);
        });
    }
};
