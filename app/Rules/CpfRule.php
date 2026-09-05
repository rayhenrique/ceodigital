<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class CpfRule implements ValidationRule
{
    /**
     * Valida o cálculo matemático dos dígitos verificadores de um CPF brasileiro.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) && ! is_numeric($value)) {
            $fail('O campo :attribute informado não é válido.');
            return;
        }

        $cpf = preg_replace('/\D/', '', (string) $value);

        // Deve conter exatamente 11 dígitos
        if (strlen($cpf) !== 11) {
            $fail('O CPF deve conter exatamente 11 dígitos numéricos.');
            return;
        }

        // Elimina sequências inválidas conhecidas (ex: 000.000.000-00, 111.111.111-11, etc.)
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            $fail('O CPF informado é inválido.');
            return;
        }

        // Cálculo do 1º dígito verificador
        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma += ((int) $cpf[$i]) * (10 - $i);
        }
        $resto = ($soma * 10) % 11;
        $digito1 = ($resto === 10 || $resto === 11) ? 0 : $resto;

        if (((int) $cpf[9]) !== $digito1) {
            $fail('O CPF informado possui dígito verificador inválido.');
            return;
        }

        // Cálculo do 2º dígito verificador
        $soma = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma += ((int) $cpf[$i]) * (11 - $i);
        }
        $resto = ($soma * 10) % 11;
        $digito2 = ($resto === 10 || $resto === 11) ? 0 : $resto;

        if (((int) $cpf[10]) !== $digito2) {
            $fail('O CPF informado possui dígito verificador inválido.');
            return;
        }
    }
}
