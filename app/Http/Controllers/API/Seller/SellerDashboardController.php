<?php

namespace App\Http\Controllers\API\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\StoreSellerLogoRequest;
use App\Http\Requests\Seller\StoreSellerPayoutAccountRequest;
use App\Http\Requests\Seller\StoreSellerVoucherRequest;
use App\Http\Requests\Seller\UpdateOnsiteAppointmentRequest;
use App\Http\Requests\Seller\UpdateSellerOrderStatusRequest;
use App\Http\Requests\Seller\UpdateSellerSettingsRequest;
use App\Http\Requests\Seller\UpdateSellerVoucherRequest;
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

    public function updateOrder(UpdateSellerOrderStatusRequest $request, string $uuid): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Order status updated.',
            'data' => ['order' => $this->service->updateOrderStatus($request->user(), $uuid, $request->validated('status'), $request)],
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

    public function storeLogo(StoreSellerLogoRequest $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Store logo updated.',
            'data' => ['settings' => $this->service->storeLogo($request->user(), $request->file('logo'), $request)],
        ]);
    }

    public function storePayoutAccount(StoreSellerPayoutAccountRequest $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Bank account saved.',
            'data' => ['bank' => $this->service->storePayoutAccount($request->user(), $request->validated(), $request)],
        ], 201);
    }

    public function destroyPayoutAccount(Request $request, string $uuid): JsonResponse
    {
        $this->service->destroyPayoutAccount($request->user(), $uuid, $request);

        return response()->json([
            'status' => true,
            'message' => 'Bank account removed.',
            'data' => [],
        ]);
    }

    public function defaultPayoutAccount(Request $request, string $uuid): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Default payout account updated.',
            'data' => ['bank' => $this->service->setDefaultPayoutAccount($request->user(), $uuid, $request)],
        ]);
    }

    public function vouchers(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Store vouchers loaded.',
            'data' => ['vouchers' => $this->service->vouchers($request->user())],
        ]);
    }

    public function storeVoucher(StoreSellerVoucherRequest $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Voucher created.',
            'data' => ['voucher' => $this->service->storeVoucher($request->user(), $request->validated(), $request)],
        ], 201);
    }

    public function updateVoucher(UpdateSellerVoucherRequest $request, string $uuid): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Voucher updated.',
            'data' => ['voucher' => $this->service->updateVoucher($request->user(), $uuid, $request->validated(), $request)],
        ]);
    }

    public function destroyVoucher(Request $request, string $uuid): JsonResponse
    {
        $this->service->destroyVoucher($request->user(), $uuid, $request);

        return response()->json([
            'status' => true,
            'message' => 'Voucher deleted.',
            'data' => [],
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

    public function importDistSku(Request $request, string $uuid): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'SKU imported to your catalog.',
            'data' => $this->service->importDistSku($request->user(), $uuid, $request),
        ], 201);
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

    public function updateOnsite(UpdateOnsiteAppointmentRequest $request, string $uuid): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Visit request updated.',
            'data' => ['appointment' => $this->service->updateOnsite($request->user(), $uuid, $request->validated(), $request)],
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

    public function issueOpenApiToken(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Open API token generated.',
            'data' => $this->service->issueOpenApiToken($request->user(), $request),
        ], 201);
    }

    public function revokeOpenApiToken(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Open API token revoked.',
            'data' => $this->service->revokeOpenApiToken($request->user(), $request),
        ]);
    }
}
