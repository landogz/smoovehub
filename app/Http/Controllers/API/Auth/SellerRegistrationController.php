<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SellerRegistrationRequest;
use App\Services\Auth\SellerRegistrationService;
use Illuminate\Http\JsonResponse;

class SellerRegistrationController extends Controller
{
    public function __construct(
        private readonly SellerRegistrationService $service,
    ) {}

    public function store(SellerRegistrationRequest $request): JsonResponse
    {
        $data = $this->service->register($request->validated(), $request);

        return response()->json([
            'status' => true,
            'message' => 'Seller account created successfully.',
            'data' => $data,
        ], 201);
    }
}
