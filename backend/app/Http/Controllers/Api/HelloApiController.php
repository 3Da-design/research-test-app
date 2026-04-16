<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ResearchSampleService;
use Illuminate\Http\JsonResponse;

class HelloApiController extends Controller
{
    public function __construct(private readonly ResearchSampleService $researchSampleService)
    {
    }

    public function hello(): JsonResponse
    {
        return response()->json($this->researchSampleService->hello());
    }

    public function items(): JsonResponse
    {
        return response()->json([
            'data' => $this->researchSampleService->items(),
        ]);
    }
}
