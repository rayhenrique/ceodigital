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
        Schema::create('dentista_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dentista_id')->constrained('dentistas')->onDelete('cascade');
            $table->unsignedTinyInteger('dia_semana');
            $table->enum('turno', ['manha', 'tarde', 'noite']);
            $table->unsignedSmallInteger('vagas_padrao')->default(8);
            $table->timestamps();

            $table->unique(['dentista_id', 'dia_semana', 'turno'], 'uk_dentista_escala');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dentista_grades');
    }
};
