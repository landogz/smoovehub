<?php

namespace App\Http\Controllers\Web\Site\ClientPayment;

use App\Http\Controllers\Controller;
use App\Services\Site\ClientPayment\ClientPaymentService;
use Illuminate\View\View;

class ClientPaymentPageController extends Controller
{
    public function __construct(
        private readonly ClientPaymentService $clientPaymentService,
    ) {}

    public function index(): View
    {
        return view('site.client-payment.index', [
            'payment' => $this->clientPaymentService->page(),
        ]);
    }
}
