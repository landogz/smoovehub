<?php

namespace App\Http\Controllers\API\LandingPage;

use App\Http\Controllers\Controller;
use App\Http\Requests\LandingPage\NewsletterSubscribeRequest;
use App\Services\LandingPage\LandingPageService;
use Illuminate\Http\JsonResponse;

class LandingPageController extends Controller
{
    public function __construct(
        private readonly LandingPageService $landingPageService,
    ) {}

    public function subscribeNewsletter(NewsletterSubscribeRequest $request): JsonResponse
    {
        $data = $this->landingPageService->subscribeNewsletter($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Subscribed successfully. Check your inbox for deals.',
            'data' => $data,
        ]);
    }
}
