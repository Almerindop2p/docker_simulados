<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_configurations', function (Blueprint $table): void {
            $table->boolean('recaptcha_enabled')->default(false)->after('feedback_feed_enabled');
            $table->text('recaptcha_site_key')->nullable()->after('recaptcha_enabled');
            $table->text('recaptcha_secret_key')->nullable()->after('recaptcha_site_key');
        });
    }

    public function down(): void
    {
        Schema::table('site_configurations', function (Blueprint $table): void {
            $table->dropColumn([
                'recaptcha_enabled',
                'recaptcha_site_key',
                'recaptcha_secret_key',
            ]);
        });
    }
};
