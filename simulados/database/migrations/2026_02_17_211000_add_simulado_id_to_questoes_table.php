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
        if (!Schema::hasTable('questoes') || Schema::hasColumn('questoes', 'simulado_id')) {
            return;
        }

        Schema::table('questoes', function (Blueprint $table) {
            $table->foreignId('simulado_id')
                ->nullable()
                ->after('instituicao_id')
                ->constrained('simulados')
                ->restrictOnDelete();

            $table->index('simulado_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('questoes') || !Schema::hasColumn('questoes', 'simulado_id')) {
            return;
        }

        Schema::table('questoes', function (Blueprint $table) {
            $table->dropForeign(['simulado_id']);
            $table->dropIndex(['simulado_id']);
            $table->dropColumn('simulado_id');
        });
    }
};

