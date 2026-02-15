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
        if (Schema::hasTable('questoes')) {
            return;
        }

        Schema::create('questoes', function (Blueprint $table) {
            $table->id();
            // RESTRICT para impedir remover banca/materia com questoes vinculadas.
            $table->foreignId('banca_id')->constrained('bancas')->restrictOnDelete();
            $table->foreignId('materia_id')->constrained('materias')->restrictOnDelete();
            $table->text('enunciado');
            $table->text('alternativa_a');
            $table->text('alternativa_b');
            $table->text('alternativa_c');
            $table->text('alternativa_d');
            $table->text('alternativa_e')->nullable();
            $table->enum('gabarito', ['A', 'B', 'C', 'D', 'E']);
            $table->text('explicacao')->nullable();
            $table->timestamps();

            $table->index('banca_id');
            $table->index('materia_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questoes');
    }
};
