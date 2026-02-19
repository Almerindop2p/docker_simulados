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
        Schema::create('route_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('consent_mode', 20)->nullable();
            $table->string('route_name')->nullable();
            $table->text('page_url');
            $table->text('path')->nullable();
            $table->text('referrer')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('browser', 120)->nullable();
            $table->string('browser_version', 40)->nullable();
            $table->string('device_type', 24)->nullable();
            $table->string('operating_system', 120)->nullable();
            $table->string('country', 120)->nullable();
            $table->string('state', 120)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('neighborhood', 120)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('timezone', 80)->nullable();
            $table->string('language', 32)->nullable();
            $table->unsignedInteger('viewport_width')->nullable();
            $table->unsignedInteger('viewport_height')->nullable();
            $table->timestamp('captured_at');
            $table->timestamps();

            $table->index(['captured_at', 'route_name']);
            $table->index(['captured_at', 'user_id']);
            $table->index('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_metrics');
    }
};
