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
        Schema::create('page_visit_counters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('anonymous_id', 64)->nullable();
            $table->string('visitor_key', 120);
            $table->string('route_name', 255)->nullablentãe();
            $table->text('page_path');
            $table->char('page_hash', 40);
            $table->string('country', 120)->nullable();
            $table->string('state', 120)->nullable();
            $table->string('city', 120)->nullable();
            $table->char('location_hash', 40);
            $table->unsignedBigInteger('visits_count')->default(0);
            $table->timestamp('first_visited_at');
            $table->timestamp('last_visited_at');
            $table->timestamps();

            $table->unique(['visitor_key', 'page_hash', 'location_hash'], 'pvc_visitor_page_location_unique');
            $table->index(['page_hash', 'last_visited_at'], 'pvc_page_hash_last_visited_idx');
            $table->index(['country', 'last_visited_at'], 'pvc_country_last_visited_idx');
            $table->index(['user_id', 'last_visited_at'], 'pvc_user_last_visited_idx');
            $table->index('anonymous_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_visit_counters');
    }
};
