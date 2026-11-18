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
        Schema::table('home_slides', function (Blueprint $table) {
            $table->string('source_type', 20)->default('manual')->after('description');
            $table->foreignId('photo_id')->nullable()->after('source_type')->constrained('photos')->nullOnDelete();
            $table->foreignId('article_id')->nullable()->after('photo_id')->constrained('articles')->nullOnDelete();
            $table->string('title')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_slides', function (Blueprint $table) {
            $table->dropForeign(['photo_id']);
            $table->dropForeign(['article_id']);
            $table->dropColumn(['photo_id', 'article_id', 'source_type']);
            $table->string('title')->nullable(false)->change();
        });
    }
};
