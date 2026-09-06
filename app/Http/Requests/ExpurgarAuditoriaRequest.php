<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpurgarAuditoriaRequest extends FormRequest
{
    /**
     * Determina se o usuário autenticado é autorizado a expurgar logs.
     * Exclusivo para administradores do sistema.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Regras de validação para o expurgo manual de auditoria.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'dias' => ['required', 'integer', 'in:30,60,90,180,365'],
            'confirmacao' => ['required', 'string', 'in:EXPURGAR'],
        ];
    }

    /**
     * Mensagens amigáveis em português brasileiro.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'dias.required' => 'O período para corte de histórico é obrigatório.',
            'dias.in' => 'Selecione um período válido para o expurgo (30, 60, 90, 180 ou 365 dias).',
            'confirmacao.required' => 'A confirmação de segurança é obrigatória.',
            'confirmacao.in' => 'Digite exatamente a palavra EXPURGAR em maiúsculas para confirmar o expurgo de logs.',
        ];
    }
}
