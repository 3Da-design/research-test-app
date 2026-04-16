<?php

namespace App\Http\Controllers\Blade;

use App\Http\Controllers\Controller;
use App\Services\ResearchSampleService;
use Illuminate\Contracts\View\View;

class HelloBladeController extends Controller
{
    public function __construct(private readonly ResearchSampleService $researchSampleService)
    {
    }

    public function hello(): View
    {
        return view('hello', [
            'message' => $this->researchSampleService->hello()['message'],
            'items' => $this->researchSampleService->items(),
        ]);
    }
}
