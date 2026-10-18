<?php

namespace App\Http\Controllers\API\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreMemberAddressRequest;
use App\Services\Member\MemberAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberAddressController extends Controller
{
    public function __construct(
        private readonly MemberAccountService $service,
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Addresses loaded.',
            'data' => ['addresses' => $this->service->addresses($request->user())],
        ]);
    }

    public function store(StoreMemberAddressRequest $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Address saved.',
            'data' => ['address' => $this->service->storeAddress($request->user(), $request->validated(), $request)],
        ], 201);
    }

    public function update(StoreMemberAddressRequest $request, string $uuid): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Address updated.',
            'data' => ['address' => $this->service->updateAddress($request->user(), $uuid, $request->validated(), $request)],
        ]);
    }

    public function destroy(Request $request, string $uuid): JsonResponse
    {
        $this->service->deleteAddress($request->user(), $uuid, $request);

        return response()->json([
            'status' => true,
            'message' => 'Address deleted.',
            'data' => [],
        ]);
    }

    public function setDefault(Request $request, string $uuid): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Default address updated.',
            'data' => ['address' => $this->service->setDefaultAddress($request->user(), $uuid, $request)],
        ]);
    }
}
