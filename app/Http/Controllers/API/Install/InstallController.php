<?php

namespace App\Http\Controllers\API\Install;

use App\Http\Controllers\Controller;
use App\Http\Requests\Install\RunInstallRequest;
use App\Services\Install\InstallService;
use Illuminate\Http\JsonResponse;
use RuntimeException;
use Throwable;

class InstallController extends Controller
{
    public function __construct(
        private readonly InstallService $installService,
    ) {}

    public function status(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Install status retrieved.',
            'data' => $this->installService->status(),
        ]);
    }

    public function store(RunInstallRequest $request): JsonResponse
    {
        try {
            $data = $this->installService->run($request->validated());

            return response()->json([
                'status' => true,
                'message' => 'SmooveHub installed successfully.',
                'data' => $data,
            ]);
        } catch (RuntimeException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => [],
            ], 422);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Installation failed unexpectedly.',
                'errors' => [],
            ], 500);
        }
    }
}
