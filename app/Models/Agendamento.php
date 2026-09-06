<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $paciente_id
 * @property int $dentista_id
 * @property int $especialidade_id
 * @property int $user_id
 * @property Carbon $data_agendamento
 * @property-read Carbon $data
 * @property string $turno
 * @property string $tipo
 * @property string $status
 * @property string|null $horario_chegada
 * @property string|null $observacao
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Paciente|null $paciente
 * @property-read Dentista|null $dentista
 * @property-read Especialidade|null $especialidade
 * @property-read User|null $user
 */
class Agendamento extends Model
{
    use HasFactory;

    /**
     * Tabela associada ao Model.
     *
     * @var string
     */
    protected $table = 'agendamentos';

    /**
     * Atributos atribuíveis em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'paciente_id',
        'dentista_id',
        'especialidade_id',
        'user_id',
        'data_agendamento',
        'turno',
        'tipo',
        'status',
        'horario_chegada',
        'observacao',
    ];

    /**
     * Mapeamento de casts dos atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data_agendamento' => 'date',
        ];
    }

    /**
     * Alias acessor de retrocompatibilidade para data_agendamento.
     */
    protected function data(): Attribute
    {
        return Attribute::make(
            get: fn (): ?Carbon => $this->data_agendamento,
        );
    }

    /**
     * Escopo para filtrar agendamentos de uma data específica (padrão: hoje).
     */
    public function scopeDoDia(Builder $query, ?string $data = null): Builder
    {
        return $query->whereDate('data_agendamento', $data ?? now()->toDateString());
    }

    /**
     * Escopo para filtrar agendamentos por turno operacional ('manha', 'tarde', 'noite').
     */
    public function scopePorTurno(Builder $query, string $turno): Builder
    {
        return $query->where('turno', $turno);
    }

    /**
     * Escopo para filtrar agendamentos por profissional dentista.
     */
    public function scopePorDentista(Builder $query, int $dentistaId): Builder
    {
        return $query->where('dentista_id', $dentistaId);
    }

    /**
     * Paciente agendado.
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    /**
     * Dentista responsável pela consulta.
     */
    public function dentista(): BelongsTo
    {
        return $this->belongsTo(Dentista::class);
    }

    /**
     * Especialidade da consulta agendada.
     */
    public function especialidade(): BelongsTo
    {
        return $this->belongsTo(Especialidade::class);
    }

    /**
     * Usuário/Operador do sistema que cadastrou o agendamento.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
