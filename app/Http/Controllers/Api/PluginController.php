<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Plugins\Registries\PluginRegistry;
use Illuminate\Http\JsonResponse;

class PluginController extends Controller
{
    public function __construct(
        protected PluginRegistry $plugins
    ) {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->plugins->all());
    }
}
