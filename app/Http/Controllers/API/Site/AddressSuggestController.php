<?php

namespace App\Http\Controllers\API\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\AddressSuggestRequest;
use App\Services\Site\AddressSuggestService;
use Illuminate\Http\JsonResponse;

class AddressSuggestController extends Controller
{
    public function __construct(
        private readonly AddressSuggestService $addressSuggestService,
    ) {}

    public function __invoke(AddressSuggestRequest $request): JsonResponse
    {
        $suggestions = $this->addressSuggestService->suggest((string) $request->validated('q'));

        return response()->json([
            'status' => true,
            'message' => 'Address suggestions loaded.',
            'data' => $suggestions,
        ]);
    }
}
