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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('address')->nullable()->after('company');
            $table->string('city')->nullable()->after('address');
            $table->string('postal_code', 10)->nullable()->after('city');
            $table->string('contact_person')->nullable()->after('postal_code');
            $table->string('contact_position')->nullable()->after('contact_person');
            $table->string('contact_phone')->nullable()->after('contact_position');
            $table->datetime('last_contact_date')->nullable()->after('contact_phone');
            $table->string('preferred_contact_method')->default('email')->after('last_contact_date');;
            $table->string('status')->default('prospect')->after('preferred_contact_method');
            $table->string('source')->nullable()->after('status');
            $table->text('contact_history')->nullable()->after('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'city',
                'postal_code',
                'contact_person',
                'contact_position',
                'contact_phone',
                'last_contact_date',
                'preferred_contact_method',
                'status',
                'source',
                'contact_history',
            ]);
        });
    }
};
