<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $paciente_id
 * @property int $especialidade_id
 * @property string $turno_preferencial
 * @property string $prioridade
 * @property string $status
 * @property \Illuminate\Support\Carbon $data_solicitacao
 * @property string|null $observacoes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $turno_preferencial_formatado
 * @property-read \App\Models\Paciente|null $paciente
 * @property-read \App\Models\Especialidade|null $especialidade
 */
class DemandaReprimida extends Model
{
    use HasFactory;

    /**
     * Tabela associada ao Model.
     *
     * @var string
     */
    protected $table = 'demanda_reprimida';

    /**
     * Atributos atribuíveis em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'paciente_id',
        'especialidade_id',
        'turno_preferencial',
        'prioridade',
        'status',
        'data_solicitacao',
        'observacoes',
    ];

    /**
     * Mapeamento de casts dos atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data_solicitacao' => 'date',
        ];
    }

    /**
     * Rótulo formatado para exibição do turno preferencial do paciente.
     */
    public function getTurnoPreferencialFormatadoAttribute(): string
    {
        return match ($this->turno_preferencial) {
            'manha' => 'Manhã',
            'tarde' => 'Tarde',
            'noite' => 'Noite',
            default => 'Qualquer Turno',
        };
    }

    /**
     * Marca o registro da fila como agendado/atendido.
     */
    public function marcarComoAgendado(): bool
    {
        return $this->update(['status' => 'agendado']);
    }

    /**
     * Marca o paciente como desistente na fila de espera.
     */
    public function marcarComoDesistente(): bool
    {
        return $this->update(['status' => 'desistente']);
    }

    /**
     * Escopo para filtrar registros que ainda estão aguardando vaga.
     */
    public function scopeAguardando(Builder $query): Builder
    {
        return $query->where('status', 'aguardando');
    }

    /**
     * Escopo para ordenar e priorizar atendimentos urgentes.
     */
    public function scopePrioridadeFila(Builder $query): Builder
    {
        return $query->orderByRaw("CASE WHEN prioridade = 'urgente' THEN 0 ELSE 1 END")
                     ->orderBy('data_solicitacao', 'asc');
    }

    /**
     * Paciente em espera.
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    /**
     * Especialidade odontológica demandada.
     */
    public function especialidade(): BelongsTo
    {
        return $this->belongsTo(Especialidade::class);
    }
}
