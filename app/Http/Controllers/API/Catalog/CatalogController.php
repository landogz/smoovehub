<?php

namespace App\Http\Controllers\API\Catalog;

use App\Http\Controllers\Controller;
use App\Services\Catalog\CatalogService;
use Illuminate\Http\JsonResponse;

class CatalogController extends Controller
{
    public function __construct(
        private readonly CatalogService $service,
    ) {}

    public function products(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Catalog retrieved.',
            'data' => $this->service->catalog(),
        ]);
    }
}
