<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Especialidade;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EspecialidadeFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $especialidade = $this->route('especialidade');
        $espId = $especialidade instanceof Especialidade ? $especialidade->id : (is_numeric($especialidade) ? (int) $especialidade : null);

        return [
            'nome' => [
                'required',
                'string',
                'min:3',
                'max:150',
                Rule::unique('especialidades', 'nome')->ignore($espId),
            ],
            'descricao' => ['nullable', 'string', 'max:1000'],
            'status_ativo' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'O nome da especialidade é obrigatório.',
            'nome.unique' => 'Esta especialidade já está cadastrada no sistema.',
        ];
    }
}
