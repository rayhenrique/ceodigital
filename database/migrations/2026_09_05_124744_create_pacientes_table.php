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
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ubs_id')->constrained('ubs')->onDelete('restrict');
            $table->string('cpf', 11)->unique('idx_pacientes_cpf');
            $table->string('cns', 15)->nullable();
            $table->string('nome_completo');
            $table->date('data_nascimento');
            $table->enum('sexo', ['M', 'F', 'Outro']);
            $table->text('endereco')->nullable();
            $table->string('telefone_1', 20);
            $table->string('telefone_2', 20)->nullable();
            $table->string('nome_acs')->nullable();
            $table->timestamps();

            $table->index('nome_completo', 'idx_pacientes_nome');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
