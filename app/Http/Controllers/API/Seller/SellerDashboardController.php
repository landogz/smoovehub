<?php

namespace App\Http\Controllers\API\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\UpdateSellerSettingsRequest;
use App\Services\Seller\SellerDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SellerDashboardController extends Controller
{
    public function __construct(
        private readonly SellerDashboardService $service,
    ) {}

    public function overview(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Dashboard overview loaded.',
            'data' => $this->service->overview($request->user()),
        ]);
    }

    public function orders(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Seller orders loaded.',
            'data' => ['orders' => $this->service->orders($request->user())],
        ]);
    }

    public function payouts(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Payout snapshot loaded.',
            'data' => $this->service->payouts($request->user()),
        ]);
    }

    public function settings(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Store settings loaded.',
            'data' => ['settings' => $this->service->settings($request->user())],
        ]);
    }

    public function updateSettings(UpdateSellerSettingsRequest $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Store settings saved.',
            'data' => ['settings' => $this->service->updateSettings($request->user(), $request->validated(), $request)],
        ]);
    }

    public function distCatalog(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Distribution catalog loaded.',
            'data' => ['items' => $this->service->distCatalog($request->user())],
        ]);
    }

    public function imports(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Import history loaded.',
            'data' => ['imports' => $this->service->imports($request->user())],
        ]);
    }

    public function purchases(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Distribution purchases loaded.',
            'data' => ['purchases' => $this->service->imports($request->user())],
        ]);
    }

    public function onsiteAppointments(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Onsite appointments loaded.',
            'data' => ['appointments' => $this->service->onsiteAppointments($request->user())],
        ]);
    }

    public function openApi(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Open API status loaded.',
            'data' => $this->service->openApi($request->user()),
        ]);
    }
}
