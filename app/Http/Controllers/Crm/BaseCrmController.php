<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

abstract class BaseCrmController extends Controller
{
    protected function crmView(string $view, array $data = []): View
    {
        return view($view, array_merge($data, [
            'crmNav' => config('crm.nav'),
            'crmThemes' => config('crm.themes'),
        ]));
    }
}
