<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_feedback_prompt_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->timestamp('cooldown_until')->nullable()->index();
            $table->timestamp('last_prompt_at')->nullable();
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamp('last_dismissed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_feedback_prompt_states');
    }
};

