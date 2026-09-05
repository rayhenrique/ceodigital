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
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('restrict');
            $table->foreignId('dentista_id')->constrained('dentistas')->onDelete('restrict');
            $table->foreignId('especialidade_id')->constrained('especialidades')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->date('data_agendamento');
            $table->enum('turno', ['manha', 'tarde', 'noite']);
            $table->enum('tipo', ['normal', 'encaixe', 'espontanea'])->default('normal');
            $table->enum('status', ['agendado', 'presente', 'em_atendimento', 'concluido', 'falta', 'cancelado'])->default('agendado');
            $table->time('horario_chegada')->nullable();
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->index(['data_agendamento', 'turno', 'dentista_id'], 'idx_agenda_busca');
            $table->index(['data_agendamento', 'status'], 'idx_agenda_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendamentos');
    }
};
