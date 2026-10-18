<?php

namespace App\Http\Controllers\API\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\RedeemMemberVoucherRequest;
use App\Services\Member\MemberAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberVoucherController extends Controller
{
    public function __construct(
        private readonly MemberAccountService $service,
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Vouchers loaded.',
            'data' => ['vouchers' => $this->service->vouchers($request->user())],
        ]);
    }

    public function redeem(RedeemMemberVoucherRequest $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Voucher added.',
            'data' => ['voucher' => $this->service->redeemVoucher($request->user(), $request->validated('code'), $request)],
        ]);
    }
}
