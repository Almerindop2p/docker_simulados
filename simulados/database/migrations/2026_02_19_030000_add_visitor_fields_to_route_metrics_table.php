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
            $table->string('anonymous_id', 64)->nullable()->after('user_id');
            $table->string('visitor_key', 120)->nullable()->after('anonymous_id');
            $table->string('device_model', 120)->nullable()->after('device_type');

            $table->index('anonymous_id');
            $table->index('visitor_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('route_metrics', function (Blueprint $table) {
            $table->dropIndex(['anonymous_id']);
            $table->dropIndex(['visitor_key']);
            $table->dropColumn(['anonymous_id', 'visitor_key', 'device_model']);
        });
    }
};
