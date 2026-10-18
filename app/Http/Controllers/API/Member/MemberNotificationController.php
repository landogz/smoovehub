<?php

namespace App\Http\Controllers\API\Member;

use App\Http\Controllers\Controller;
use App\Services\Member\MemberAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberNotificationController extends Controller
{
    public function __construct(
        private readonly MemberAccountService $service,
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Notifications loaded.',
            'data' => ['notifications' => $this->service->notifications($request->user())],
        ]);
    }

    public function markRead(Request $request, string $id): JsonResponse
    {
        $this->service->markNotificationRead($request->user(), $id);

        return response()->json([
            'status' => true,
            'message' => 'Notification marked read.',
            'data' => [],
        ]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $this->service->markAllNotificationsRead($request->user());

        return response()->json([
            'status' => true,
            'message' => 'All notifications marked read.',
            'data' => [],
        ]);
    }
}
