<?php

namespace App\Http\Controllers\API\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\StoreSellerProductRequest;
use App\Services\Seller\SellerProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SellerProductController extends Controller
{
    public function __construct(
        private readonly SellerProductService $service,
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Seller products loaded.',
            'data' => ['products' => $this->service->index($request->user())],
        ]);
    }

    public function store(StoreSellerProductRequest $request): JsonResponse
    {
        $product = $this->service->store($request->user(), $request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Product listed successfully.',
            'data' => ['product' => $product],
        ], 201);
    }

    public function destroy(Request $request, string $uuid): JsonResponse
    {
        $this->service->destroy($request->user(), $uuid, $request);

        return response()->json([
            'status' => true,
            'message' => 'Product removed.',
            'data' => [],
        ]);
    }
}
