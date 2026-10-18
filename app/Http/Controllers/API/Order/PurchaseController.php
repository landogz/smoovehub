<?php

namespace App\Http\Controllers\API\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\PurchaseRequest;
use App\Http\Requests\Order\QuoteRequest;
use App\Services\Order\PurchaseService;
use Illuminate\Http\JsonResponse;

class PurchaseController extends Controller
{
    public function __construct(
        private readonly PurchaseService $service,
    ) {}

    public function quote(QuoteRequest $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Checkout quote calculated.',
            'data' => $this->service->quote($request->validated()),
        ]);
    }

    public function store(PurchaseRequest $request): JsonResponse
    {
        $data = $this->service->complete($request->user(), $request->validated(), $request);

        return response()->json([
            'status' => true,
            'message' => 'Order placed. Payment confirmation ships with Stripe in Phase 3.',
            'data' => $data,
        ], 201);
    }
}
