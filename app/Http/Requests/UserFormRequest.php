<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $user = $this->route('user');
        $userId = $user instanceof User ? $user->id : (is_numeric($user) ? (int) $user : null);

        $rules = [
            'name' => ['required', 'string', 'min:3', 'max:191'],
            'email' => [
                'required',
                'string',
                'email',
                'max:191',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'role' => ['required', Rule::in(['admin', 'operador'])],
            'status_ativo' => ['nullable', 'boolean'],
        ];

        if ($this->isMethod('post')) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        } else {
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome do usuário é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.unique' => 'Este endereço de e-mail já está em uso.',
            'password.required' => 'A senha de acesso é obrigatória.',
            'password.min' => 'A senha deve conter no mínimo 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'role.required' => 'O perfil de acesso (admin ou operador) é obrigatório.',
        ];
    }
}
