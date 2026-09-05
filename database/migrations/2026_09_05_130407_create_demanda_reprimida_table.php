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
        Schema::create('demanda_reprimida', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->foreignId('especialidade_id')->constrained('especialidades')->onDelete('restrict');
            $table->enum('turno_preferencial', ['qualquer', 'manha', 'tarde', 'noite'])->default('qualquer');
            $table->enum('prioridade', ['normal', 'urgente'])->default('normal');
            $table->enum('status', ['aguardando', 'agendado', 'desistente'])->default('aguardando');
            $table->date('data_solicitacao');
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->index(['especialidade_id', 'status', 'prioridade'], 'idx_espera_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demanda_reprimida');
    }
};
