<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Paciente;
use App\Rules\CpfRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PacienteFormRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a realizar esta requisição.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Sanitiza os dados antes da aplicação das regras de validação.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('cpf') && is_string($this->cpf)) {
            $this->merge([
                'cpf' => preg_replace('/\D/', '', $this->cpf),
            ]);
        }

        if ($this->has('cns') && is_string($this->cns)) {
            $cnsLimpo = preg_replace('/\D/', '', $this->cns);
            $this->merge([
                'cns' => $cnsLimpo !== '' ? $cnsLimpo : null,
            ]);
        }
    }

    /**
     * Regras de validação da requisição.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $paciente = $this->route('paciente');
        $pacienteId = $paciente instanceof Paciente ? $paciente->id : (is_numeric($paciente) ? (int) $paciente : null);

        return [
            'ubs_id' => ['required', 'integer', 'exists:ubs,id'],
            'cpf' => [
                'required',
                'string',
                'size:11',
                new CpfRule(),
                Rule::unique('pacientes', 'cpf')->ignore($pacienteId),
            ],
            'cns' => ['nullable', 'string', 'size:15'],
            'nome_completo' => ['required', 'string', 'min:3', 'max:191'],
            'data_nascimento' => ['required', 'date', 'before_or_equal:today'],
            'sexo' => ['required', Rule::in(['M', 'F', 'Outro'])],
            'endereco' => ['nullable', 'string', 'max:1000'],
            'telefone_1' => ['required', 'string', 'max:20'],
            'telefone_2' => ['nullable', 'string', 'max:20'],
            'nome_acs' => ['nullable', 'string', 'max:191'],
        ];
    }

    /**
     * Mensagens de validação customizadas em português.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ubs_id.required' => 'A UBS de origem é obrigatória.',
            'ubs_id.exists' => 'A UBS selecionada é inválida.',
            'cpf.required' => 'O CPF do paciente é obrigatório.',
            'cpf.size' => 'O CPF deve possuir exatamente 11 dígitos numéricos.',
            'cpf.unique' => 'Este CPF já está cadastrado para outro paciente.',
            'cns.size' => 'O Cartão Nacional de Saúde (CNS) deve conter exatamente 15 dígitos.',
            'nome_completo.required' => 'O nome completo do paciente é obrigatório.',
            'data_nascimento.required' => 'A data de nascimento é obrigatória.',
            'data_nascimento.before_or_equal' => 'A data de nascimento não pode ser uma data futura.',
            'sexo.required' => 'O sexo do paciente é obrigatório.',
            'sexo.in' => 'O campo sexo deve ser M, F ou Outro.',
            'telefone_1.required' => 'O telefone principal é obrigatório.',
        ];
    }
}
