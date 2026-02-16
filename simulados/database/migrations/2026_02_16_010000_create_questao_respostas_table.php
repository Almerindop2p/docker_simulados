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
        if (Schema::hasTable('questao_respostas')) {
            return;
        }

        Schema::create('questao_respostas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('questao_id')->nullable()->constrained('questoes')->nullOnDelete();
            $table->foreignId('banca_id')->nullable()->constrained('bancas')->nullOnDelete();
            $table->foreignId('materia_id')->nullable()->constrained('materias')->nullOnDelete();
            $table->char('resposta_marcada', 1);
            $table->char('gabarito', 1);
            $table->boolean('acertou');
            $table->timestamp('respondida_em')->useCurrent();
            $table->timestamps();

            $table->index(['user_id', 'respondida_em']);
            $table->index(['user_id', 'acertou', 'respondida_em']);
            $table->index(['user_id', 'materia_id', 'respondida_em']);
            $table->index(['user_id', 'questao_id', 'respondida_em']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questao_respostas');
    }
};