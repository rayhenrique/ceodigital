<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Ubs;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UbsFormRequest extends FormRequest
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
        $ubs = $this->route('ub') ?? $this->route('ubs');
        $ubsId = $ubs instanceof Ubs ? $ubs->id : (is_numeric($ubs) ? (int) $ubs : null);

        return [
            'nome' => [
                'required',
                'string',
                'min:3',
                'max:191',
                Rule::unique('ubs', 'nome')->ignore($ubsId),
            ],
            'endereco' => ['nullable', 'string', 'max:191'],
            'diretor' => ['nullable', 'string', 'max:191'],
            'contato' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'O nome da UBS é obrigatório.',
            'nome.unique' => 'Já existe uma UBS cadastrada com este nome.',
            'nome.max' => 'O nome da UBS não pode exceder 191 caracteres.',
        ];
    }
}
