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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('garden_project_id')->constrained()->cascadeOnDelete();
            $table->string('number')->unique();
            $table->string('title');
            $table->string('status')->default('draft');
            $table->string('currency', 3)->default('PLN');
            $table->date('valid_until')->nullable();
            $table->decimal('labor_cost', 12, 2)->default(0);
            $table->decimal('material_cost', 12, 2)->default(0);
            $table->decimal('margin_percent', 5, 2)->default(20);
            $table->decimal('total_net', 12, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(23);
            $table->decimal('total_gross', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
