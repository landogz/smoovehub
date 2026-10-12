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

    public function store(StoreSellerProductRequest $request): JsonResponse
    {
        $product = $this->service->store($request->user(), $request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Product listed successfully.',
            'data' => ['product' => $product],
        ], 201);
    }
}
