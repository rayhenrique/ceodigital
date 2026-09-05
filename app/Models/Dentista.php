<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dentista extends Model
{
    use HasFactory;

    /**
     * Tabela associada ao Model.
     *
     * @var string
     */
    protected $table = 'dentistas';

    /**
     * Atributos atribuíveis em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'especialidade_id',
        'nome_completo',
        'cro',
        'telefone',
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
     * Accessor para compatibilidade do nome completo.
     */
    protected function nome(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn (): string => (string) $this->nome_completo,
        );
    }

    /**
     * Escopo para filtrar dentistas ativos.
     */
    public function scopeAtivos(Builder $query): Builder
    {
        return $query->where('status_ativo', true);
    }

    /**
     * Especialidade odontológica do dentista.
     */
    public function especialidade(): BelongsTo
    {
        return $this->belongsTo(Especialidade::class);
    }

    /**
     * Grade/escala de atendimento do dentista por dia da semana e turno.
     */
    public function grades(): HasMany
    {
        return $this->hasMany(DentistaGrade::class);
    }

    /**
     * Histórico de agendamentos atribuídos ao dentista.
     */
    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class);
    }
}
