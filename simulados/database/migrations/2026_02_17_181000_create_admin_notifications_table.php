<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('category', 30)->default('system');
            $table->string('title', 160);
            $table->text('message');
            $table->json('data')->nullable();
            $table->string('reference_key', 180)->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'read_at']);
            $table->index(['user_id', 'category']);
            $table->unique(['user_id', 'reference_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};

