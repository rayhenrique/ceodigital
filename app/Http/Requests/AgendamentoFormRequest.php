<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Dentista;
use App\Models\DentistaGrade;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class AgendamentoFormRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a realizar esta requisição.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação do agendamento.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'paciente_id' => ['required', 'integer', 'exists:pacientes,id'],
            'dentista_id' => ['required', 'integer', 'exists:dentistas,id'],
            'especialidade_id' => ['required', 'integer', 'exists:especialidades,id'],
            'data_agendamento' => ['required', 'date', 'after_or_equal:today'],
            'turno' => ['required', Rule::in(['manha', 'tarde', 'noite'])],
            'tipo' => ['required', Rule::in(['normal', 'encaixe', 'espontanea'])],
            'observacao' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Validações adicionais de regra de negócio (dia da semana, escala e compatibilidade de especialidade).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $data = $this->input('data_agendamento');
            $turno = $this->input('turno');
            $dentistaId = $this->input('dentista_id');
            $especialidadeId = $this->input('especialidade_id');

            if (! $data || ! $turno || ! $dentistaId) {
                return;
            }

            try {
                $carbonData = Carbon::parse($data);
            } catch (\Throwable) {
                return;
            }

            $diaSemana = $carbonData->dayOfWeekIso; // 1 (Segunda) a 7 (Domingo)

            if ($diaSemana === 7) {
                $v->errors()->add('data_agendamento', 'O CEO não realiza agendamentos aos domingos.');
                return;
            }

            // Validação de compatibilidade da especialidade do profissional
            $dentista = Dentista::find($dentistaId);
            if ($dentista && $especialidadeId && (int) $dentista->especialidade_id !== (int) $especialidadeId) {
                $v->errors()->add('dentista_id', 'O dentista selecionado não pertence à especialidade informada.');
            }

            // Validação da grade de atendimento do dentista
            $gradeExiste = DentistaGrade::where('dentista_id', $dentistaId)
                ->where('dia_semana', $diaSemana)
                ->where('turno', $turno)
                ->exists();

            if (! $gradeExiste) {
                $v->errors()->add(
                    'turno',
                    'O dentista selecionado não possui escala de atendimento cadastrada para este dia da semana e turno.'
                );
            }
        });
    }

    /**
     * Mensagens de validação em português.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'paciente_id.required' => 'O paciente deve ser informado.',
            'paciente_id.exists' => 'O paciente selecionado não foi encontrado.',
            'dentista_id.required' => 'O dentista responsável deve ser informado.',
            'dentista_id.exists' => 'O dentista selecionado não foi encontrado.',
            'especialidade_id.required' => 'A especialidade odontológica é obrigatória.',
            'especialidade_id.exists' => 'A especialidade informada é inválida.',
            'data_agendamento.required' => 'A data do agendamento é obrigatória.',
            'data_agendamento.after_or_equal' => 'A data da consulta não pode ser retroativa.',
            'turno.required' => 'O turno da consulta é obrigatório.',
            'turno.in' => 'O turno selecionado é inválido (permitido: manha, tarde ou noite).',
            'tipo.required' => 'O tipo de agendamento é obrigatório.',
            'tipo.in' => 'Tipo de agendamento inválido.',
        ];
    }
}
