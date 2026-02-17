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
        if (!Schema::hasTable('questoes') || Schema::hasColumn('questoes', 'instituicao_id')) {
            return;
        }

        Schema::table('questoes', function (Blueprint $table) {
            $table->foreignId('instituicao_id')
                ->nullable()
                ->after('materia_id')
                ->constrained('instituicoes')
                ->restrictOnDelete();

            $table->index('instituicao_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('questoes') || !Schema::hasColumn('questoes', 'instituicao_id')) {
            return;
        }

        Schema::table('questoes', function (Blueprint $table) {
            $table->dropForeign(['instituicao_id']);
            $table->dropIndex(['instituicao_id']);
            $table->dropColumn('instituicao_id');
        });
    }
};

