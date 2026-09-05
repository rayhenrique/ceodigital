<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DemandaReprimidaFormRequest extends FormRequest
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
        return [
            'paciente_id' => ['required', 'integer', 'exists:pacientes,id'],
            'especialidade_id' => ['required', 'integer', 'exists:especialidades,id'],
            'turno_preferencial' => ['required', Rule::in(['qualquer', 'manha', 'tarde', 'noite'])],
            'prioridade' => ['required', Rule::in(['normal', 'urgente'])],
            'data_solicitacao' => ['required', 'date', 'before_or_equal:today'],
            'observacoes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'paciente_id.required' => 'O paciente deve ser informado.',
            'paciente_id.exists' => 'Paciente inválido.',
            'especialidade_id.required' => 'A especialidade demandada é obrigatória.',
            'especialidade_id.exists' => 'Especialidade selecionada inválida.',
            'data_solicitacao.required' => 'A data da solicitação é obrigatória.',
            'data_solicitacao.before_or_equal' => 'A data de solicitação não pode ser uma data futura.',
        ];
    }
}
