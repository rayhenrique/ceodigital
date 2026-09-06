<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetUserPasswordRequest extends FormRequest
{
    /**
     * Determina se o usuário autenticado é autorizado a fazer a requisição.
     * Exclusivo para administradores do sistema.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Regras de validação para a redefinição de senha.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Mensagens de validação em português brasileiro.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'password.required' => 'A nova senha de acesso é obrigatória.',
            'password.min' => 'A nova senha deve conter no mínimo 8 caracteres.',
            'password.confirmed' => 'A confirmação da nova senha não confere.',
        ];
    }
}
