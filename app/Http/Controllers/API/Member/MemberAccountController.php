<?php

namespace App\Http\Controllers\API\Member;

use App\Http\Controllers\Controller;
use App\Services\Member\MemberAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberAccountController extends Controller
{
    public function __construct(
        private readonly MemberAccountService $service,
    ) {}

    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Member account loaded.',
            'data' => $this->service->overview($request->user()),
        ]);
    }
}
