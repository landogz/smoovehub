<?php

namespace App\Http\Controllers\API\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreMemberAvatarRequest;
use App\Http\Requests\Member\UpdateMemberPasswordRequest;
use App\Http\Requests\Member\UpdateMemberProfileRequest;
use App\Http\Requests\Member\UpdateMemberSettingsRequest;
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

    public function updateProfile(UpdateMemberProfileRequest $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Profile saved.',
            'data' => [
                'member' => $this->service->updateProfile($request->user(), $request->validated(), $request),
            ],
        ]);
    }

    public function storeAvatar(StoreMemberAvatarRequest $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Photo updated.',
            'data' => $this->service->storeAvatar($request->user(), $request->file('avatar'), $request),
        ]);
    }

    public function updatePassword(UpdateMemberPasswordRequest $request): JsonResponse
    {
        $this->service->updatePassword($request->user(), $request->validated(), $request);

        return response()->json([
            'status' => true,
            'message' => 'Password updated.',
            'data' => [],
        ]);
    }

    public function updateSettings(UpdateMemberSettingsRequest $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Settings saved.',
            'data' => [
                'settings' => $this->service->updateSettings($request->user(), $request->validated(), $request),
            ],
        ]);
    }
}
