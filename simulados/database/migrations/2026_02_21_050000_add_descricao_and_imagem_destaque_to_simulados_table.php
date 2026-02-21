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
        if (!Schema::hasTable('simulados')) {
            return;
        }

        Schema::table('simulados', function (Blueprint $table) {
            if (!Schema::hasColumn('simulados', 'descricao')) {
                $table->text('descricao')->nullable()->after('visibilidade');
            }

            if (!Schema::hasColumn('simulados', 'imagem_destaque_path')) {
                $table->string('imagem_destaque_path', 255)->nullable()->after('descricao');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('simulados')) {
            return;
        }

        Schema::table('simulados', function (Blueprint $table) {
            if (Schema::hasColumn('simulados', 'imagem_destaque_path')) {
                $table->dropColumn('imagem_destaque_path');
            }

            if (Schema::hasColumn('simulados', 'descricao')) {
                $table->dropColumn('descricao');
            }
        });
    }
};

