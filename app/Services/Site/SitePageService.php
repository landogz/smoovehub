<?php

namespace App\Services\Site;

use App\Repositories\Site\DesignMe\DesignMeOrderRepository;
use App\Repositories\Site\DriverApplicationRepository;
use App\Repositories\Site\SitePageRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SitePageService
{
    public function __construct(
        private readonly SitePageRepository $repository,
        private readonly DriverApplicationRepository $driverApplicationRepository,
        private readonly QuoteEstimateService $quoteEstimateService,
        private readonly DesignMeOrderRepository $designMeOrderRepository,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function home(): array
    {
        return $this->repository->homePage();
    }

    /**
     * @return array<string, mixed>
     */
    public function shop(): array
    {
        return $this->repository->shopPage();
    }

    /**
     * @return array<string, mixed>
     */
    public function about(): array
    {
        return $this->repository->aboutPage();
    }

    /**
     * @return array<string, mixed>
     */
    public function sell(): array
    {
        return $this->repository->sellPage();
    }

    /**
     * @return array<string, mixed>
     */
    public function sellers(): array
    {
        return $this->repository->sellersPage();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function sellerStore(string $slug): ?array
    {
        $seller = $this->repository->findSeller($slug);

        if (! $seller) {
            return null;
        }

        return $this->repository->sellerStorePage($seller);
    }

    /**
     * @return array<string, mixed>
     */
    public function cities(): array
    {
        return $this->repository->citiesPage();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function cityShow(string $slug): ?array
    {
        $city = $this->repository->findCity($slug);

        if (! $city) {
            return null;
        }

        return $this->repository->cityShowPage($city);
    }

    /**
     * @return array<string, mixed>
     */
    public function blog(): array
    {
        return $this->repository->blogPage();
    }

    /**
     * @return array<string, mixed>
     */
    public function privacy(): array
    {
        return $this->repository->privacyPage();
    }

    /**
     * @return array<string, mixed>
     */
    public function terms(): array
    {
        return $this->repository->termsPage();
    }

    /**
     * @return array<string, mixed>
     */
    public function contact(): array
    {
        return $this->repository->contactPage();
    }

    /**
     * @return array<string, mixed>
     */
    public function quote(): array
    {
        return $this->repository->quotePage();
    }

    /**
     * @return array<string, mixed>
     */
    public function drive(): array
    {
        return $this->repository->drivePage();
    }

    /**
     * @return array<string, mixed>
     */
    public function designRoom(): array
    {
        return $this->repository->designRoomPage();
    }

    /**
     * @return array<string, mixed>
     */
    public function designMe(): array
    {
        return $this->repository->designMePage();
    }

    /**
     * @return array<string, mixed>
     */
    public function account(): array
    {
        return $this->repository->accountPage();
    }

    /**
     * @return array<string, mixed>
     */
    public function dashboard(): array
    {
        return $this->repository->dashboardPage();
    }

    /**
     * @return array<string, mixed>
     */
    public function distribute(): array
    {
        return $this->repository->distributePage();
    }

    /**
     * @return array<string, mixed>
     */
    public function admin(): array
    {
        return $this->repository->adminPage();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function subscribeNewsletter(array $payload): array
    {
        $validated = Validator::make($payload, [
            'email' => ['required', 'email', 'max:255'],
        ])->validate();

        $this->repository->storeNewsletterEmail($validated['email']);

        return ['email' => $validated['email']];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function submitContact(array $payload): array
    {
        $validated = Validator::make($payload, [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'string', 'max:50', Rule::notIn([''])],
            'subject' => ['required', 'string', 'max:255'],
            'order_number' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:5000'],
        ])->validate();

        return $validated;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function submitDriverApplication(array $payload): array
    {
        $validated = Validator::make($payload, [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('drivers', 'email')],
            'phone' => ['required', 'string', 'max:30'],
            'vehicle_type' => ['required', Rule::in(['car', 'van', 'pickup', 'boxtruck', 'semitruck'])],
            'mc_number' => ['nullable', 'string', 'max:30'],
            'dot_number' => [
                Rule::requiredIf(fn () => in_array($payload['vehicle_type'] ?? '', ['boxtruck', 'semitruck'], true)),
                'nullable',
                'string',
                'max:30',
            ],
            'payout_method' => ['required', Rule::in(['ach', 'stripe', 'zelle', 'venmo'])],
            'account_holder' => [Rule::requiredIf(fn () => ($payload['payout_method'] ?? '') === 'ach'), 'nullable', 'string', 'max:120'],
            'bank_name' => [Rule::requiredIf(fn () => ($payload['payout_method'] ?? '') === 'ach'), 'nullable', 'string', 'max:120'],
            'routing_number' => [Rule::requiredIf(fn () => ($payload['payout_method'] ?? '') === 'ach'), 'nullable', 'digits:9'],
            'account_number' => [Rule::requiredIf(fn () => ($payload['payout_method'] ?? '') === 'ach'), 'nullable', 'string', 'min:6', 'max:20'],
            'account_type' => ['nullable', Rule::in(['checking', 'savings'])],
            'stripe_email' => [Rule::requiredIf(fn () => ($payload['payout_method'] ?? '') === 'stripe'), 'nullable', 'email', 'max:255'],
            'zelle_contact' => [Rule::requiredIf(fn () => ($payload['payout_method'] ?? '') === 'zelle'), 'nullable', 'string', 'max:120'],
            'venmo_handle' => [Rule::requiredIf(fn () => ($payload['payout_method'] ?? '') === 'venmo'), 'nullable', 'string', 'max:60'],
        ])->validate();

        $driver = $this->driverApplicationRepository->create($validated);

        return [
            'uuid' => $driver->uuid,
            'email' => $driver->email,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function estimateQuote(array $payload): array
    {
        $validated = Validator::make($payload, [
            'pickup_zip' => ['required', 'digits:5'],
            'delivery_zip' => ['required', 'digits:5'],
            'door_width' => ['required', 'numeric', 'min:18', 'max:120'],
            'door_height' => ['required', 'numeric', 'min:60', 'max:120'],
            'weight' => ['nullable', 'numeric', 'min:1'],
            'pieces' => ['nullable', 'integer', 'min:1'],
            'stairs_enabled' => ['nullable', 'boolean'],
            'stair_flights' => ['nullable', 'integer', 'min:0', 'max:20'],
            'stair_type' => ['nullable', Rule::in(['standard', 'spiral', 'outdoor'])],
            'elevator_enabled' => ['nullable', 'boolean'],
            'elevator_floors' => ['nullable', 'integer', 'min:0', 'max:50'],
            'elevator_width' => ['nullable', 'numeric', 'min:24', 'max:120'],
            'carry_distance' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'delivery_type' => ['nullable', Rule::in(['CURBSIDE', 'IN_HOME'])],
            'name' => ['nullable', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
        ])->validate();

        return $this->quoteEstimateService->estimate($validated);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function submitDesignMeOrder(array $payload): array
    {
        $validated = Validator::make($payload, [
            'furniture_type' => ['required', Rule::in(['sofa', 'table', 'bed', 'desk', 'chair', 'shelf', 'cabinet', 'outdoor'])],
            'material' => ['required', 'string', 'max:50'],
            'color' => ['required', 'string', 'max:20'],
            'color_name' => ['required', 'string', 'max:80'],
            'finish' => ['required', 'string', 'max:50'],
            'width' => ['required', 'integer', 'min:12', 'max:200'],
            'depth' => ['required', 'integer', 'min:10', 'max:120'],
            'height' => ['required', 'integer', 'min:10', 'max:120'],
            'features' => ['nullable', 'array'],
            'timeline' => ['required', Rule::in(['standard', 'rush'])],
            'delivery_method' => ['required', Rule::in(['standard', 'whiteglove'])],
            'contact_name' => ['required', 'string', 'max:120'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:30'],
            'contact_address' => ['required', 'string', 'max:500'],
            'source' => ['nullable', 'string', 'max:80'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'estimated_price' => ['required', 'numeric', 'min:0'],
            'deposit_amount' => ['required', 'numeric', 'min:0'],
            'sketch_data' => ['nullable', 'string'],
            'reference_images' => ['nullable', 'array', 'max:5'],
            'reference_images.*' => ['nullable', 'string'],
        ])->validate();

        $order = $this->designMeOrderRepository->create($validated);

        return [
            'uuid' => $order->uuid,
            'reference' => 'DM-'.strtoupper(substr(str_replace('-', '', $order->uuid), 0, 8)),
            'estimated_price' => (float) $order->estimated_price,
            'deposit_amount' => (float) $order->deposit_amount,
        ];
    }
}
