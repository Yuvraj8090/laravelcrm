<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Themes\Registries\ThemeRegistry;
use Illuminate\Http\JsonResponse;

class ThemeController extends Controller
{
    public function __construct(
        protected ThemeRegistry $themes
    ) {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->themes->all());
    }
}
