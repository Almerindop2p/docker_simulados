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
        if (!Schema::hasTable('questoes')) {
            return;
        }

        $schema = Schema::getConnection()->getSchemaBuilder();

        if (!$schema->hasIndex('questoes', 'questoes_filtro_home_idx')) {
            Schema::table('questoes', function (Blueprint $table) {
                // Otimiza os filtros combinados da pagina inicial (banca + materia + ordenacao recente).
                $table->index(['banca_id', 'materia_id', 'created_at'], 'questoes_filtro_home_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('questoes')) {
            return;
        }

        $schema = Schema::getConnection()->getSchemaBuilder();

        if ($schema->hasIndex('questoes', 'questoes_filtro_home_idx')) {
            Schema::table('questoes', function (Blueprint $table) {
                $table->dropIndex('questoes_filtro_home_idx');
            });
        }
    }
};
