<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paciente extends Model
{
    use HasFactory;

    /**
     * Tabela associada ao Model.
     *
     * @var string
     */
    protected $table = 'pacientes';

    /**
     * Atributos atribuíveis em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ubs_id',
        'cpf',
        'cns',
        'nome_completo',
        'data_nascimento',
        'sexo',
        'endereco',
        'telefone_1',
        'telefone_2',
        'nome_acs',
    ];

    /**
     * Mapeamento de casts dos atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data_nascimento' => 'date',
        ];
    }

    /**
     * Mutator para sanitizar o CPF antes da persistência, garantindo apenas dígitos numéricos.
     */
    protected function cpf(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value): ?string => $value !== null ? preg_replace('/\D/', '', $value) : null,
        );
    }

    /**
     * Mutator opcional para sanitizar o CNS antes da persistência, mantendo apenas dígitos numéricos.
     */
    protected function cns(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value): ?string => $value !== null ? preg_replace('/\D/', '', $value) : null,
        );
    }

    /**
     * Accessor para compatibilidade do nome completo.
     */
    protected function nome(): Attribute
    {
        return Attribute::make(
            get: fn (): string => (string) $this->nome_completo,
        );
    }

    /**
     * Accessor para exibir o CPF formatado com pontuação (XXX.XXX.XXX-XX).
     */
    protected function cpfFormatado(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $raw = (string) $this->cpf;
                if (strlen($raw) === 11) {
                    return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $raw) ?? $raw;
                }
                return $raw;
            }
        );
    }

    /**
     * UBS de referência / encaminhamento do paciente.
     */
    public function ubs(): BelongsTo
    {
        return $this->belongsTo(Ubs::class, 'ubs_id');
    }

    /**
     * Histórico de agendamentos do paciente.
     */
    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class);
    }

    /**
     * Registros de espera na demanda reprimida do paciente.
     */
    public function demandasReprimidas(): HasMany
    {
        return $this->hasMany(DemandaReprimida::class);
    }
}
