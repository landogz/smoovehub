<?php

namespace App\Http\Controllers\API\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\PurchaseRequest;
use App\Services\Order\PurchaseService;
use Illuminate\Http\JsonResponse;

class PurchaseController extends Controller
{
    public function __construct(
        private readonly PurchaseService $service,
    ) {}

    public function store(PurchaseRequest $request): JsonResponse
    {
        $order = $this->service->complete($request->user(), $request->validated(), $request);

        return response()->json([
            'status' => true,
            'message' => 'Payment confirmed. Order placed successfully.',
            'data' => ['order' => $order],
        ], 201);
    }
}
