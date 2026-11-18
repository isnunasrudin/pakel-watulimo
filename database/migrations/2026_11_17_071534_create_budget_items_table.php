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
        Schema::create('budget_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->string('section', 20);
            $table->string('group')->nullable();
            $table->string('label');
            $table->unsignedBigInteger('budget_amount')->default(0);
            $table->unsignedBigInteger('realization_amount')->default(0);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->index(['year', 'section', 'order']);
            $table->index(['year', 'section', 'group']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_items');
    }
};
