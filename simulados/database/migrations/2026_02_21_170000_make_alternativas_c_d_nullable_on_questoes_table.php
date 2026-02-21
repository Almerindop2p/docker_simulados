<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questoes', function (Blueprint $table) {
            $table->text('alternativa_c')->nullable()->change();
            $table->text('alternativa_d')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('questoes')
            ->whereNull('alternativa_c')
            ->update(['alternativa_c' => '']);

        DB::table('questoes')
            ->whereNull('alternativa_d')
            ->update(['alternativa_d' => '']);

        Schema::table('questoes', function (Blueprint $table) {
            $table->text('alternativa_c')->nullable(false)->change();
            $table->text('alternativa_d')->nullable(false)->change();
        });
    }
};
