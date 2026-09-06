<?php

declare(strict_types=1);

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
        Schema::table('auditorias', function (Blueprint $table) {
            $table->index('created_at', 'idx_auditorias_created_at');
            $table->index(['tabela_afetada', 'registro_id'], 'idx_auditorias_entidade');
            $table->index(['tabela_afetada', 'created_at'], 'idx_auditorias_tabela_data');
            $table->index(['user_id', 'created_at'], 'idx_auditorias_user_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auditorias', function (Blueprint $table) {
            $table->dropIndex('idx_auditorias_created_at');
            $table->dropIndex('idx_auditorias_entidade');
            $table->dropIndex('idx_auditorias_tabela_data');
            $table->dropIndex('idx_auditorias_user_data');
        });
    }
};
