<?php

namespace App\Repositories\LandingPage;

use App\Models\Content\NewsletterSubscription;

class LandingPageRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function categories(): array
    {
        return [
            ['icon' => '🏢', 'name' => 'Commercial', 'subtitle' => '87 items', 'href' => '#shop'],
            ['icon' => '🏠', 'name' => 'Residential', 'subtitle' => '142 items', 'href' => '#shop'],
            ['icon' => '🔌', 'name' => 'Appliances', 'subtitle' => '65 items', 'href' => '#shop'],
            ['icon' => '✨', 'name' => 'Brand New', 'subtitle' => '+40% markup', 'href' => '#shop'],
            ['icon' => '💎', 'name' => 'Thrift Finds', 'subtitle' => '+10% markup', 'href' => '#shop'],
            ['icon' => '⚡', 'name' => 'Same-Day', 'subtitle' => 'Select zips', 'href' => '#shop'],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function products(): array
    {
        $fromDb = app(\App\Repositories\Catalog\CatalogRepository::class)->storefront();

        if ($fromDb !== []) {
            return $fromDb;
        }

        return [
            ['id' => 1, 'name' => 'Japandi Solid Oak Dining Table', 'price' => 1249, 'original' => 1780, 'seller' => 'HomeStyle Co.', 'condition' => 'BRAND_NEW', 'icon' => '🪑', 'trending' => true, 'same_day' => false],
            ['id' => 2, 'name' => 'Industrial Steel Office Desk Set', 'price' => 849, 'original' => 1200, 'seller' => 'OfficeFirst', 'condition' => 'BRAND_NEW', 'icon' => '🖥️', 'trending' => false, 'same_day' => true],
            ['id' => 3, 'name' => 'Plush Velvet Sectional Sofa', 'price' => 2190, 'original' => 3100, 'seller' => 'LuxLiving', 'condition' => 'FLOOR_MODEL', 'icon' => '🛋️', 'trending' => true, 'same_day' => false],
            ['id' => 4, 'name' => 'Mid-Century Walnut Bookshelf', 'price' => 398, 'original' => 0, 'seller' => 'Thrift Palace', 'condition' => 'THRIFT', 'icon' => '📚', 'trending' => false, 'same_day' => false],
            ['id' => 5, 'name' => 'Commercial Restaurant Booth Set', 'price' => 1640, 'original' => 2200, 'seller' => 'BizFurniture', 'condition' => 'BRAND_NEW', 'icon' => '🍽️', 'trending' => false, 'same_day' => true],
            ['id' => 6, 'name' => 'Smart Refrigerator French Door', 'price' => 2890, 'original' => 3400, 'seller' => 'ApplianceHub TX', 'condition' => 'BRAND_NEW', 'icon' => '🧊', 'trending' => true, 'same_day' => false],
            ['id' => 7, 'name' => 'Adjustable Standing Desk Pro', 'price' => 699, 'original' => 900, 'seller' => 'OfficeFirst', 'condition' => 'REFURBISHED', 'icon' => '💻', 'trending' => false, 'same_day' => true],
            ['id' => 8, 'name' => 'Minimalist Linen King Bed Frame', 'price' => 1099, 'original' => 1500, 'seller' => 'HomeStyle Co.', 'condition' => 'BRAND_NEW', 'icon' => '🛏️', 'trending' => false, 'same_day' => false],
            ['id' => 9, 'name' => 'Italian Leather Executive Chair', 'price' => 599, 'original' => 850, 'seller' => 'BizFurniture', 'condition' => 'FLOOR_MODEL', 'icon' => '🪑', 'trending' => true, 'same_day' => true],
            ['id' => 10, 'name' => 'Farmhouse Entryway Bench', 'price' => 219, 'original' => 0, 'seller' => 'Thrift Palace', 'condition' => 'THRIFT', 'icon' => '🪵', 'trending' => false, 'same_day' => false],
            ['id' => 11, 'name' => 'Stainless Commercial Dishwasher', 'price' => 1880, 'original' => 2400, 'seller' => 'ApplianceHub TX', 'condition' => 'BRAND_NEW', 'icon' => '🍽️', 'trending' => false, 'same_day' => false],
            ['id' => 12, 'name' => 'Biophilic Rattan Accent Chair', 'price' => 449, 'original' => 620, 'seller' => 'HomeStyle Co.', 'condition' => 'BRAND_NEW', 'icon' => '🌿', 'trending' => true, 'same_day' => true],
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function conditionLabels(): array
    {
        return [
            'BRAND_NEW' => ['label' => 'New', 'class' => 'sh-badge-blue'],
            'THRIFT' => ['label' => 'Thrift', 'class' => 'sh-badge-amber'],
            'FLOOR_MODEL' => ['label' => 'Floor', 'class' => 'sh-badge-slate'],
            'REFURBISHED' => ['label' => 'Refurb', 'class' => 'sh-badge-green'],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function deliveryTiers(): array
    {
        return [
            ['range' => '0–50 mi', 'status' => 'Auto-approved', 'tone' => 'ok'],
            ['range' => '51–100 mi', 'status' => 'CRM confirm', 'tone' => 'warn'],
            ['range' => '101–200 mi', 'status' => 'Validation req.', 'tone' => 'warn'],
            ['range' => '200+ mi', 'status' => 'Custom quote', 'tone' => 'danger'],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function voiceQuestions(): array
    {
        return [
            ['question' => 'Where can I find a sofa near me?', 'href' => '#shop'],
            ['question' => 'What furniture stores deliver same day?', 'href' => '#shop'],
            ['question' => 'How much does furniture delivery cost?', 'href' => '#quote'],
            ['question' => 'Where can I buy office chairs in bulk?', 'href' => '#shop'],
            ['question' => 'What are trending furniture styles?', 'href' => '#shop'],
            ['question' => 'Where can I find thrift furniture near me?', 'href' => '#shop'],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function vendors(): array
    {
        return [
            ['initial' => 'H', 'name' => 'HomeStyle Co.', 'type' => 'Residential', 'rating' => '4.9', 'avatar_class' => 'sh-vendor-avatar-blue'],
            ['initial' => 'O', 'name' => 'OfficeFirst', 'type' => 'Commercial', 'rating' => '4.8', 'avatar_class' => 'sh-vendor-avatar-green'],
            ['initial' => 'T', 'name' => 'Thrift Palace', 'type' => 'Thrift', 'rating' => '4.7', 'avatar_class' => 'sh-vendor-avatar-amber'],
            ['initial' => 'A', 'name' => 'ApplianceHub TX', 'type' => 'Appliances', 'rating' => '4.9', 'avatar_class' => 'sh-vendor-avatar-orange'],
            ['initial' => 'L', 'name' => 'LuxLiving', 'type' => 'Residential', 'rating' => '4.8', 'avatar_class' => 'sh-vendor-avatar-purple'],
            ['initial' => 'B', 'name' => 'BizFurniture', 'type' => 'Commercial', 'rating' => '4.9', 'avatar_class' => 'sh-vendor-avatar-blue'],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function testimonials(): array
    {
        return [
            [
                'quote' => 'Bought a full office set for my restaurant. Delivered next day — the driver kept me updated the entire time.',
                'name' => 'Maria K.',
                'role' => 'Restaurant owner · Dallas, TX',
                'initial' => 'M',
                'avatar_class' => 'sh-testimonial-avatar-blue',
            ],
            [
                'quote' => 'The thrift section is gold — got a Restoration Hardware sofa for 40% less than retail.',
                'name' => 'James T.',
                'role' => 'Interior designer · Miami, FL',
                'initial' => 'J',
                'avatar_class' => 'sh-testimonial-avatar-amber',
            ],
            [
                'quote' => 'Price at checkout matched the quote exactly — no surprise fees. That trust sold me.',
                'name' => 'Sandra R.',
                'role' => 'Homeowner · Chicago, IL',
                'initial' => 'S',
                'avatar_class' => 'sh-testimonial-avatar-green',
            ],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function navLinks(): array
    {
        return [
            ['label' => 'Home', 'href' => '/', 'class' => ''],
            ['label' => 'About', 'href' => '#about', 'class' => ''],
            ['label' => 'Shop', 'href' => '#shop', 'class' => ''],
            ['label' => 'Sellers', 'href' => '#sellers', 'class' => ''],
            ['label' => 'Cities', 'href' => '#cities', 'class' => ''],
            ['label' => 'Blog', 'href' => '#blog', 'class' => ''],
            ['label' => 'Drive', 'href' => '#drive', 'class' => ''],
            ['label' => 'Sell', 'href' => '#sell', 'class' => 'sh-nav-link-sell'],
            ['label' => 'Quote', 'href' => '#quote', 'class' => ''],
            ['label' => 'Contact', 'href' => '#contact', 'class' => ''],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function footerColumns(): array
    {
        return [
            [
                'title' => 'Shop',
                'links' => [
                    ['label' => 'All furniture', 'href' => '#shop'],
                    ['label' => 'Commercial', 'href' => '#shop'],
                    ['label' => 'Residential', 'href' => '#shop'],
                    ['label' => 'Thrift finds', 'href' => '#shop'],
                ],
            ],
            [
                'title' => 'Services',
                'links' => [
                    ['label' => 'Delivery quote', 'href' => '#quote'],
                    ['label' => 'Track order', 'href' => '#'],
                    ['label' => 'Become a driver', 'href' => '#drive'],
                    ['label' => 'Sell on SmooveHub', 'href' => '#sell'],
                ],
            ],
            [
                'title' => 'Cities',
                'links' => [
                    ['label' => 'Dallas, TX', 'href' => '#cities'],
                    ['label' => 'New York, NY', 'href' => '#cities'],
                    ['label' => 'Houston, TX', 'href' => '#cities'],
                    ['label' => 'Miami, FL', 'href' => '#cities'],
                ],
            ],
            [
                'title' => 'Company',
                'links' => [
                    ['label' => 'About us', 'href' => '#about'],
                    ['label' => 'Blog', 'href' => '#blog'],
                    ['label' => 'Contact', 'href' => '#contact'],
                    ['label' => 'Privacy Policy', 'href' => '#privacy'],
                    ['label' => 'Terms of Service', 'href' => '#terms'],
                ],
            ],
        ];
    }

    public function storeNewsletterEmail(string $email): void
    {
        NewsletterSubscription::query()->updateOrCreate(
            ['email' => $email],
            [
                'city' => null,
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
            ]
        );
    }
}
