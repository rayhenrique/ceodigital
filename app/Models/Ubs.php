<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ubs extends Model
{
    use HasFactory;

    /**
     * Tabela associada ao Model.
     *
     * @var string
     */
    protected $table = 'ubs';

    /**
     * Atributos atribuíveis em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nome',
        'endereco',
        'diretor',
        'contato',
    ];

    /**
     * Pacientes vinculados a esta UBS de origem.
     */
    public function pacientes(): HasMany
    {
        return $this->hasMany(Paciente::class);
    }
}
