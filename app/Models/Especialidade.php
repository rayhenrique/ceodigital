<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Especialidade extends Model
{
    use HasFactory;

    /**
     * Tabela associada ao Model.
     *
     * @var string
     */
    protected $table = 'especialidades';

    /**
     * Atributos atribuíveis em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nome',
        'descricao',
        'status_ativo',
    ];

    /**
     * Mapeamento de casts dos atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status_ativo' => 'boolean',
        ];
    }

    /**
     * Escopo para filtrar apenas especialidades ativas.
     */
    public function scopeAtivas(Builder $query): Builder
    {
        return $query->where('status_ativo', true);
    }

    /**
     * Dentistas vinculados a esta especialidade.
     */
    public function dentistas(): HasMany
    {
        return $this->hasMany(Dentista::class);
    }

    /**
     * Agendamentos realizados para esta especialidade.
     */
    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class);
    }

    /**
     * Pacientes na lista de espera para esta especialidade.
     */
    public function demandasReprimidas(): HasMany
    {
        return $this->hasMany(DemandaReprimida::class);
    }
}
