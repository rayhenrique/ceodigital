<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Dentista;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DentistaFormRequest extends FormRequest
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
        $dentista = $this->route('dentista');
        $dentistaId = $dentista instanceof Dentista ? $dentista->id : (is_numeric($dentista) ? (int) $dentista : null);

        return [
            'especialidade_id' => ['required', 'integer', 'exists:especialidades,id'],
            'nome_completo' => ['required', 'string', 'min:3', 'max:191'],
            'cro' => [
                'required',
                'string',
                'max:30',
                Rule::unique('dentistas', 'cro')->ignore($dentistaId),
            ],
            'telefone' => ['nullable', 'string', 'max:20'],
            'status_ativo' => ['nullable', 'boolean'],
            'grades' => ['nullable', 'array'],
            'grades.*.dia_semana' => ['required_with:grades', 'integer', 'between:1,6'],
            'grades.*.turno' => ['required_with:grades', Rule::in(['manha', 'tarde', 'noite'])],
            'grades.*.vagas_padrao' => ['required_with:grades', 'integer', 'min:1', 'max:50'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'especialidade_id.required' => 'A especialidade do dentista é obrigatória.',
            'nome_completo.required' => 'O nome completo do profissional é obrigatório.',
            'cro.required' => 'O número do CRO é obrigatório.',
            'cro.unique' => 'Este número de CRO já está cadastrado para outro profissional.',
        ];
    }
}
