<?php

namespace App\Http\Controllers\API\Member;

use App\Http\Controllers\Controller;
use App\Services\Member\MemberAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberOrderController extends Controller
{
    public function __construct(
        private readonly MemberAccountService $service,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $tab = (string) $request->query('tab', 'all');
        $query = trim((string) $request->query('q', ''));

        return response()->json([
            'status' => true,
            'message' => 'Purchases loaded.',
            'data' => ['orders' => $this->service->orders($request->user(), $tab, $query)],
        ]);
    }
}
