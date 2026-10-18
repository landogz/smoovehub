<?php

namespace App\Http\Controllers\API\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreMemberPaymentMethodRequest;
use App\Services\Member\MemberAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberPaymentMethodController extends Controller
{
    public function __construct(
        private readonly MemberAccountService $service,
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Payment methods loaded.',
            'data' => ['payment_methods' => $this->service->paymentMethods($request->user())],
        ]);
    }

    public function store(StoreMemberPaymentMethodRequest $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Payment method saved.',
            'data' => ['payment_method' => $this->service->storePaymentMethod($request->user(), $request->validated(), $request)],
        ], 201);
    }

    public function destroy(Request $request, string $uuid): JsonResponse
    {
        $this->service->deletePaymentMethod($request->user(), $uuid, $request);

        return response()->json([
            'status' => true,
            'message' => 'Payment method deleted.',
            'data' => [],
        ]);
    }

    public function setDefault(Request $request, string $uuid): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Default payment method updated.',
            'data' => ['payment_method' => $this->service->setDefaultPaymentMethod($request->user(), $uuid, $request)],
        ]);
    }
}
