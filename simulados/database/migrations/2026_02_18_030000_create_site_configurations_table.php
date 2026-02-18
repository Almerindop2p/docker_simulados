<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_configurations', function (Blueprint $table): void {
            $table->id();
            $table->boolean('adsense_enabled')->default(false);
            $table->longText('adsense_head_script')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_configurations');
    }
};
