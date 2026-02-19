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
        Schema::table('user_metric_consents', function (Blueprint $table) {
            $table->string('country', 120)->nullable()->after('user_agent');
            $table->string('state', 120)->nullable()->after('country');
            $table->string('city', 120)->nullable()->after('state');
            $table->string('neighborhood', 120)->nullable()->after('city');
            $table->decimal('latitude', 10, 7)->nullable()->after('neighborhood');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_metric_consents', function (Blueprint $table) {
            $table->dropColumn([
                'country',
                'state',
                'city',
                'neighborhood',
                'latitude',
                'longitude',
            ]);
        });
    }
};
