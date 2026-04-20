<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('section_elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('garden_section_id')->constrained()->onDelete('cascade');
            $table->string('type'); // trawnik, ogrodzenie, rabata, etc.
            $table->string('name');
            $table->string('material')->nullable();
            $table->decimal('quantity', 10, 2)->default(0);
            $table->string('unit', 20)->default('m²');
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('section_elements');
    }
};
