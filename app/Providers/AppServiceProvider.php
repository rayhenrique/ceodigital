<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Agendamento;
use App\Models\DemandaReprimida;
use App\Models\Dentista;
use App\Models\DentistaGrade;
use App\Models\Paciente;
use App\Models\User;
use App\Observers\AuditoriaObserver;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Mapeamento de Policies de Autorização
        Gate::policy(User::class, UserPolicy::class);

        // Registro de Auditoria para modelos sensíveis
        Agendamento::observe(AuditoriaObserver::class);
        Paciente::observe(AuditoriaObserver::class);
        DemandaReprimida::observe(AuditoriaObserver::class);
        Dentista::observe(AuditoriaObserver::class);
        DentistaGrade::observe(AuditoriaObserver::class);
        User::observe(AuditoriaObserver::class);
    }
}
