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
        Schema::table('route_metrics', function (Blueprint $table) {
            $table->string('country_code', 2)->nullable()->after('country');
            $table->index('country_code');
        });

        Schema::table('page_visit_counters', function (Blueprint $table) {
            $table->string('country_code', 2)->nullable()->after('country');
            $table->index(['country_code', 'last_visited_at'], 'pvc_country_code_last_visited_idx');
        });

        Schema::table('user_metric_consents', function (Blueprint $table) {
            $table->string('country_code', 2)->nullable()->after('country');
            $table->index('country_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_metric_consents', function (Blueprint $table) {
            $table->dropIndex(['country_code']);
            $table->dropColumn('country_code');
        });

        Schema::table('page_visit_counters', function (Blueprint $table) {
            $table->dropIndex('pvc_country_code_last_visited_idx');
            $table->dropColumn('country_code');
        });

        Schema::table('route_metrics', function (Blueprint $table) {
            $table->dropIndex(['country_code']);
            $table->dropColumn('country_code');
        });
    }
};
