<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Dias de Retenção do Log de Auditoria
    |--------------------------------------------------------------------------
    |
    | Define por quantos dias os registros de auditoria serão mantidos na base
    | de dados antes do expurgo automático realizado pelo scheduler do Laravel.
    |
    */
    'retention_days' => (int) env('AUDIT_RETENTION_DAYS', 180),
];
