<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auditoria extends Model
{
    use HasFactory;

    /**
     * Tabela associada ao Model.
     *
     * @var string
     */
    protected $table = 'auditorias';

    /**
     * Indica se o model deve manter timestamps gerenciados automaticamente.
     * Auditoria é imutável e gerencia apenas created_at.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Atributos atribuíveis em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'acao',
        'tabela_afetada',
        'registro_id',
        'dados_anteriores',
        'dados_novos',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    /**
     * Mapeamento de casts dos atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dados_anteriores' => 'array',
            'dados_novos' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Usuário que realizou a operação auditada.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
