<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_configurations', function (Blueprint $table): void {
            $table->longText('custom_html_code')->nullable()->after('adsense_head_script');
        });
    }

    public function down(): void
    {
        Schema::table('site_configurations', function (Blueprint $table): void {
            $table->dropColumn('custom_html_code');
        });
    }
};
