<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('simulado_tentativa_respostas')) {
            return;
        }

        Schema::create('simulado_tentativa_respostas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tentativa_id')->constrained('simulado_tentativas')->cascadeOnDelete();
            $table->foreignId('questao_id')->nullable()->constrained('questoes')->nullOnDelete();
            $table->unsignedInteger('question_index')->default(0);
            $table->char('resposta_marcada', 1)->nullable();
            $table->char('gabarito', 1)->nullable();
            $table->boolean('acertou')->default(false);
            $table->unsignedInteger('elapsed_seconds')->default(0);
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->unique(['tentativa_id', 'question_index'], 'simulado_tentativa_respostas_unique_idx');
            $table->index(['tentativa_id', 'questao_id'], 'simulado_tentativa_respostas_tentativa_questao_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simulado_tentativa_respostas');
    }
};

