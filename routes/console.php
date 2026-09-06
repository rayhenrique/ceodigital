<?php

declare(strict_types=1);

use App\Models\Auditoria;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Agendamento diário de expurgo automático de logs antigos de auditoria (por padrão > 180 dias).
 */
Schedule::command('model:prune', [
    '--model' => [Auditoria::class],
])->dailyAt('03:00')->name('prune-auditorias');

/**
 * Comando manual para expurgo imediato de registros antigos de auditoria via terminal.
 */
Artisan::command('audit:purge {--days=180 : Quantidade de dias a preservar}', function () {
    $days = (int) $this->option('days');
    $dataLimite = now()->subDays($days);

    $total = Auditoria::where('created_at', '<=', $dataLimite)->count();

    if ($total === 0) {
        $this->info("Nenhum registro de auditoria anterior a {$dataLimite->format('d/m/Y')} ({$days} dias) para expurgar.");
        return;
    }

    $deletados = Auditoria::where('created_at', '<=', $dataLimite)->delete();
    $this->info("Sucesso: {$deletados} registros de auditoria anteriores a {$dataLimite->format('d/m/Y')} foram expurgados.");
})->purpose('Expurga registros antigos de auditoria mantendo apenas os últimos N dias');
