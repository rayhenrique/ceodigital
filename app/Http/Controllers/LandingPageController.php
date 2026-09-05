<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Especialidade;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    /**
     * Exibe a página inicial institucional do CEO com especialidades ativas e horários.
     */
    public function __invoke(): View
    {
        $especialidades = Especialidade::ativas()
            ->orderBy('nome')
            ->get();

        return view('landing', compact('especialidades'));
    }
}
