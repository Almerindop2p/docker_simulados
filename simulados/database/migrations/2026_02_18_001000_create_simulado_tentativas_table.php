<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('simulado_tentativas')) {
            return;
        }

        Schema::create('simulado_tentativas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('simulado_id')->constrained('simulados')->cascadeOnDelete();
            $table->string('status', 20)->default('aberto');
            $table->json('questoes_snapshot');
            $table->unsignedInteger('total_questoes')->default(0);
            $table->unsignedInteger('questoes_respondidas')->default(0);
            $table->unsignedInteger('acertos')->default(0);
            $table->unsignedInteger('erros')->default(0);
            $table->unsignedInteger('current_index')->default(0);
            $table->unsignedInteger('total_elapsed_seconds')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'simulado_id', 'status'], 'simulado_tentativas_user_simulado_status_idx');
            $table->index(['simulado_id', 'status'], 'simulado_tentativas_simulado_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simulado_tentativas');
    }
};

