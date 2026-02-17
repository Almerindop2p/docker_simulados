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
        if (!Schema::hasTable('questoes') || Schema::hasColumn('questoes', 'imagem_path')) {
            return;
        }

        Schema::table('questoes', function (Blueprint $table) {
            $table->string('imagem_path')->nullable()->after('instituicao_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('questoes') || !Schema::hasColumn('questoes', 'imagem_path')) {
            return;
        }

        Schema::table('questoes', function (Blueprint $table) {
            $table->dropColumn('imagem_path');
        });
    }
};

