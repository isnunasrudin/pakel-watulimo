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
        Schema::create('village_statistics', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('value', 80);
            $table->string('unit')->nullable();
            $table->string('icon')->nullable();
            $table->string('group')->default('umum');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->index(['group', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('village_statistics');
    }
};
