<?php

namespace App\Http\Controllers\API\Site;

use App\Http\Controllers\Controller;
use App\Services\Site\SitePageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteFormController extends Controller
{
    public function __construct(
        private readonly SitePageService $sitePageService,
    ) {}

    public function contact(Request $request): JsonResponse
    {
        $data = $this->sitePageService->submitContact($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Message sent successfully. We will respond within 2 business hours.',
            'data' => ['ticket' => 'CNT-'.strtoupper(substr(md5($data['email'].now()), 0, 8))],
        ]);
    }

    public function driverApplication(Request $request): JsonResponse
    {
        $this->sitePageService->submitDriverApplication($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Driver application submitted. CRS will review within 48 hours.',
            'data' => [],
        ], 201);
    }

    public function quoteEstimate(Request $request): JsonResponse
    {
        $data = $this->sitePageService->estimateQuote($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Delivery estimate calculated.',
            'data' => $data,
        ]);
    }

    public function designMeOrder(Request $request): JsonResponse
    {
        $data = $this->sitePageService->submitDesignMeOrder($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Custom order submitted. Our craftsmen will confirm your quote within 24 hours.',
            'data' => $data,
        ], 201);
    }
}
