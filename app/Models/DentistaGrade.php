<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DentistaGrade extends Model
{
    use HasFactory;

    /**
     * Tabela associada ao Model.
     *
     * @var string
     */
    protected $table = 'dentista_grades';

    /**
     * Atributos atribuíveis em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'dentista_id',
        'dia_semana',
        'turno',
        'vagas_padrao',
    ];

    /**
     * Mapeamento de casts dos atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dia_semana' => 'integer',
            'vagas_padrao' => 'integer',
        ];
    }

    /**
     * Dentista proprietário da grade.
     */
    public function dentista(): BelongsTo
    {
        return $this->belongsTo(Dentista::class);
    }

    /**
     * Retorna a descrição legível em português do dia da semana (1 = Segunda ... 6 = Sábado).
     */
    public function getDiaSemanaTextoAttribute(): string
    {
        return match ($this->dia_semana) {
            1 => 'Segunda-feira',
            2 => 'Terça-feira',
            3 => 'Quarta-feira',
            4 => 'Quinta-feira',
            5 => 'Sexta-feira',
            6 => 'Sábado',
            7 => 'Domingo',
            default => 'Dia Desconhecido',
        };
    }
}
