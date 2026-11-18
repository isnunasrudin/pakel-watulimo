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
        Schema::table('village_settings', function (Blueprint $table) {
            $table->string('hero_badge', 255)->nullable()->after('description');
            $table->string('hero_title', 255)->nullable()->after('hero_badge');
            $table->string('hero_highlight', 255)->nullable()->after('hero_title');
            $table->text('hero_description')->nullable()->after('hero_highlight');
            $table->string('hero_primary_label', 100)->nullable()->after('hero_description');
            $table->text('hero_primary_url')->nullable()->after('hero_primary_label');
            $table->string('hero_secondary_label', 100)->nullable()->after('hero_primary_url');
            $table->text('hero_secondary_url')->nullable()->after('hero_secondary_label');
            $table->string('home_news_title', 255)->nullable()->after('hero_secondary_url');
            $table->string('home_news_link_label', 100)->nullable()->after('home_news_title');
            $table->string('home_finance_title', 255)->nullable()->after('home_news_link_label');
            $table->string('home_statistics_title', 255)->nullable()->after('home_finance_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('village_settings', function (Blueprint $table) {
            $table->dropColumn([
                'hero_badge',
                'hero_title',
                'hero_highlight',
                'hero_description',
                'hero_primary_label',
                'hero_primary_url',
                'hero_secondary_label',
                'hero_secondary_url',
                'home_news_title',
                'home_news_link_label',
                'home_finance_title',
                'home_statistics_title',
            ]);
        });
    }
};
