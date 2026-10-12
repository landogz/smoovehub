<?php

namespace App\Services\LandingPage;

use App\Repositories\LandingPage\LandingPageRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LandingPageService
{
    public function __construct(
        private readonly LandingPageRepository $repository,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function getLandingData(): array
    {
        $products = $this->repository->products();

        return [
            'hero' => [
                'badge' => '⚡ AI-Powered · 180+ Verified Sellers',
                'title' => ['Furniture', "You'll Love.", 'Delivered Fast.'],
                'highlight' => "You'll Love.",
                'subtitle' => 'Shop commercial, residential, and appliance furniture from 180+ verified sellers. Same-day delivery. CRM-coordinated. Stripe-secured.',
                'stats' => [
                    ['value' => '180+', 'label' => 'Verified sellers'],
                    ['value' => '$2.4M+', 'label' => 'Products sold'],
                    ['value' => '48hr', 'label' => 'Avg delivery time'],
                    ['value' => '100%', 'label' => 'CRM tracked'],
                ],
            ],
            'categories' => $this->repository->categories(),
            'featured_products' => array_slice($products, 0, 8),
            'trending_products' => array_values(array_filter($products, fn (array $p) => $p['trending'])),
            'delivery_tiers' => $this->repository->deliveryTiers(),
            'voice_questions' => $this->repository->voiceQuestions(),
            'vendors' => $this->repository->vendors(),
            'testimonials' => $this->repository->testimonials(),
            'nav_links' => $this->repository->navLinks(),
            'footer_columns' => $this->repository->footerColumns(),
            'condition_labels' => $this->repository->conditionLabels(),
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     *
     * @throws ValidationException
     */
    public function subscribeNewsletter(array $payload): array
    {
        $validated = Validator::make($payload, [
            'email' => ['required', 'email', 'max:255'],
        ])->validate();

        $this->repository->storeNewsletterEmail($validated['email']);

        return [
            'email' => $validated['email'],
        ];
    }
}
