<?php

namespace App\Repositories\Site;

use App\Models\Content\NewsletterSubscription;
use App\Models\Seller\SellerProfile;
use Illuminate\Support\Facades\Storage;

class SitePageRepository
{
    /**
     * @return array<int, array<string, string>>
     */
    public function navLinks(): array
    {
        return [
            ['key' => 'home', 'label' => 'Home', 'route' => 'site.home', 'class' => ''],
            ['key' => 'about', 'label' => 'About', 'route' => 'site.about', 'class' => ''],
            ['key' => 'shop', 'label' => 'Shop', 'route' => 'site.shop', 'class' => ''],
            ['key' => 'sellers', 'label' => 'Sellers', 'route' => 'site.sellers', 'class' => ''],
            ['key' => 'cities', 'label' => 'Cities', 'route' => 'site.cities', 'class' => ''],
            ['key' => 'blog', 'label' => 'Blog', 'route' => 'site.blog', 'class' => ''],
            ['key' => 'drive', 'label' => 'Drive', 'route' => 'site.drive', 'class' => ''],
            ['key' => 'sell', 'label' => 'Sell', 'route' => 'site.sell', 'class' => 'sh-nav-link-sell'],
            ['key' => 'quote', 'label' => 'Quote', 'route' => 'site.quote', 'class' => ''],
            ['key' => 'contact', 'label' => 'Contact', 'route' => 'site.contact', 'class' => ''],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function navPills(): array
    {
        return [
            ['label' => '🛋️ Design Room', 'route' => 'site.design-room', 'class' => 'sh-nav-pill-purple'],
            ['label' => '🔨 Design Me', 'route' => 'site.design-me', 'class' => 'sh-nav-pill-orange'],
            ['label' => '🏭 Distribute', 'route' => 'site.distribute', 'class' => 'sh-nav-pill-navy'],
            ['label' => '🛡 Admin', 'route' => 'site.admin', 'class' => 'sh-nav-pill-red'],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function footerColumns(): array
    {
        return [
            [
                'title' => 'Shop',
                'links' => [
                    ['label' => 'All furniture', 'route' => 'site.shop'],
                    ['label' => 'Commercial', 'route' => 'site.shop'],
                    ['label' => 'Residential', 'route' => 'site.shop'],
                    ['label' => 'Thrift finds', 'route' => 'site.shop'],
                ],
            ],
            [
                'title' => 'Services',
                'links' => [
                    ['label' => 'Delivery quote', 'route' => 'site.quote'],
                    ['label' => 'Track order', 'route' => 'site.contact'],
                    ['label' => 'Become a driver', 'route' => 'site.drive'],
                    ['label' => 'Sell on SmooveHub', 'route' => 'site.sell'],
                ],
            ],
            [
                'title' => 'Cities',
                'links' => [
                    ['label' => 'Dallas, TX', 'route' => 'site.cities.show', 'params' => ['city' => 'dallas-tx']],
                    ['label' => 'New York, NY', 'route' => 'site.cities.show', 'params' => ['city' => 'new-york-ny']],
                    ['label' => 'Houston, TX', 'route' => 'site.cities.show', 'params' => ['city' => 'houston-tx']],
                    ['label' => 'Miami, FL', 'route' => 'site.cities.show', 'params' => ['city' => 'miami-fl']],
                ],
            ],
            [
                'title' => 'Company',
                'links' => [
                    ['label' => 'About us', 'route' => 'site.about'],
                    ['label' => 'Blog', 'route' => 'site.blog'],
                    ['label' => 'Contact', 'route' => 'site.contact'],
                    ['label' => 'Privacy Policy', 'route' => 'site.privacy'],
                    ['label' => 'Terms of Service', 'route' => 'site.terms'],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function sharedLayout(): array
    {
        return [
            'nav_links' => $this->navLinks(),
            'nav_pills' => $this->navPills(),
            'footer_columns' => $this->footerColumns(),
            'condition_labels' => $this->conditionLabels(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function homePage(): array
    {
        $products = $this->products();

        return array_merge($this->sharedLayout(), [
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
            'categories' => $this->categories(),
            'featured_products' => array_slice($products, 0, 8),
            'catalog_products' => $products,
            'trending_products' => array_values(array_filter($products, fn (array $p) => $p['trending'])),
            'delivery_tiers' => $this->deliveryTiers(),
            'voice_questions' => $this->voiceQuestions(),
            'vendors' => $this->vendors(),
            'testimonials' => $this->testimonials(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function shopPage(): array
    {
        return array_merge($this->sharedLayout(), [
            'products' => $this->products(),
            'catalog_products' => $this->products(),
            'filters' => $this->shopFilters(),
            'sort_options' => ['Featured', 'Newest', 'Price: Low–High', 'Price: High–Low', 'Trending'],
            'total' => count($this->products()),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function aboutPage(): array
    {
        return array_merge($this->sharedLayout(), [
            'hero_badge' => '🏪 Established 2026 · Multi-Vendor Furniture Marketplace',
            'hero_subtitle' => 'A modern multi-vendor furniture marketplace built to simplify how people buy, sell, and deliver furniture online.',
            'intro' => [
                'Established in 2026, SmooveHub was created with one goal in mind: to help furniture stores, thrift shops, distribution companies, and independent sellers increase visibility, streamline operations, and connect with more customers through one powerful platform.',
                'We combine technology, logistics coordination, and marketplace innovation to create a seamless experience for both sellers and buyers.',
            ],
            'hero_stats' => [
                ['value' => '180+', 'label' => 'Verified Sellers', 'tone' => 'white'],
                ['value' => '100%', 'label' => 'CRM-Tracked Deliveries', 'tone' => 'white'],
                ['value' => '$2.4M+', 'label' => 'Products Sold', 'tone' => 'orange'],
                ['value' => '4.9★', 'label' => 'Avg Seller Rating', 'tone' => 'green'],
            ],
            'story_cards' => [
                ['icon' => '🏪', 'title' => 'Multi-Vendor', 'text' => 'One platform for hundreds of furniture sellers', 'tone' => 'navy'],
                ['icon' => '🚛', 'title' => 'CRS Delivery', 'text' => 'Live CRM-coordinated delivery on every order', 'tone' => 'orange'],
                ['icon' => '🤖', 'title' => 'AI-Powered', 'text' => 'SEO optimization + trend detection built in', 'tone' => 'green'],
                ['icon' => '⚡', 'title' => 'Same-Day', 'text' => 'Same-day delivery in select zip codes', 'tone' => 'amber'],
            ],
            'buyer_features' => [
                ['icon' => '🪑', 'title' => 'Browse furniture from multiple vendors', 'text' => 'Commercial, residential, appliances, thrift — all in one place', 'route' => 'site.shop'],
                ['icon' => '💳', 'title' => 'Purchase products online securely', 'text' => 'Stripe-secured checkout with 3D authentication', 'route' => 'site.shop'],
                ['icon' => '📦', 'title' => 'Request delivery quotes', 'text' => 'Door dimensions, stairs, elevator — exact pricing from CRS', 'route' => 'site.quote'],
                ['icon' => '🚛', 'title' => 'Schedule furniture deliveries', 'text' => 'Curbside, in-home, or same-day via CRS network', 'route' => 'site.quote'],
                ['icon' => '📍', 'title' => 'Track orders & delivery updates', 'text' => 'Real-time SMS and email updates from driver assignment to delivery'],
                ['icon' => '⭐', 'title' => 'Connect with trusted furniture sellers', 'text' => 'All sellers verified, rated, and Stripe Connect onboarded'],
            ],
            'vendor_features' => [
                ['icon' => '🌐', 'title' => 'Online storefront visibility', 'text' => 'Your store live in front of thousands of buyers nationwide', 'route' => 'site.sell'],
                ['icon' => '🛒', 'title' => 'Multi-vendor selling tools', 'text' => 'Dashboard, orders, notifications, payouts — all in one place', 'route' => 'site.sell'],
                ['icon' => '📋', 'title' => 'Inventory management', 'text' => 'List, edit, and track all your products with real-time updates'],
                ['icon' => '🚛', 'title' => 'Delivery coordination', 'text' => 'CRS handles routing — you just hand off to the driver'],
                ['icon' => '🤖', 'title' => 'AI-powered SEO optimization', 'text' => 'Auto-generated product metadata, keywords, and city targeting'],
                ['icon' => '💰', 'title' => 'Automated delivery quoting systems', 'text' => 'Instant quotes with door, stair, elevator pricing built in'],
            ],
            'building_for' => [
                ['icon' => '📈', 'title' => 'Seller Growth', 'text' => 'Tools that help sellers list, rank, and sell faster'],
                ['icon' => '🛒', 'title' => 'Customer Experience', 'text' => 'Browse, buy, track — simplified from start to finish'],
                ['icon' => '🚛', 'title' => 'Delivery Efficiency', 'text' => 'CRS-powered routing, real-time tracking, smart dispatch'],
                ['icon' => '🔍', 'title' => 'Marketplace Visibility', 'text' => 'AI-powered SEO that gets products found on Google'],
                ['icon' => '⚙️', 'title' => 'Operational Automation', 'text' => 'Quotes, payouts, notifications — all automated'],
            ],
            'pillars' => [
                ['icon' => '🏪', 'title' => 'Multi-Vendor Marketplace', 'text' => 'A centralized platform connecting customers with trusted furniture businesses, thrift shops, and independent resellers.', 'hover' => 'blue'],
                ['icon' => '🚛', 'title' => 'Delivery Coordination', 'text' => 'Integrated delivery quoting and logistics support powered through advanced CRM synchronization with Competitive Relocation Service.', 'hover' => 'orange'],
                ['icon' => '🤖', 'title' => 'AI & SEO Optimization', 'text' => 'Built with modern AI-powered visibility systems to help products rank better online and reach more customers across all major search platforms.', 'hover' => 'purple'],
                ['icon' => '😊', 'title' => 'Customer Convenience', 'text' => 'From browsing to delivery scheduling, we simplify the entire furniture buying experience. Search, buy, quote, track — all in one platform.', 'hover' => 'green'],
                ['icon' => '📡', 'title' => 'Scalable Technology', 'text' => 'Designed to support local sellers today while scaling into a nationwide marketplace platform. Built on Laravel, MySQL, Stripe, and AI.', 'hover' => 'blue'],
            ],
            'audience_cards' => [
                ['icon' => '🏬', 'title' => 'Furniture Store', 'text' => 'Multi-vendor selling tools', 'route' => 'site.sell'],
                ['icon' => '♻️', 'title' => 'Thrift Business', 'text' => 'Thrift & consignment tools', 'route' => 'site.sell'],
                ['icon' => '🏭', 'title' => 'Distributor', 'text' => 'Bulk listing & wholesale', 'route' => 'site.sell'],
                ['icon' => '🧑‍💼', 'title' => 'Reseller', 'text' => 'Independent seller tools', 'route' => 'site.sell'],
                ['icon' => '🛋️', 'title' => 'Customer', 'text' => 'Quality furniture, delivered', 'route' => 'site.shop'],
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function sellPage(): array
    {
        return array_merge($this->sharedLayout(), [
            'hero_subtitle' => 'Join 180+ verified sellers. List furniture, set your prices, get paid weekly via Stripe Connect.',
            'benefits' => [
                ['icon' => '📸', 'title' => 'List in minutes', 'text' => 'Upload photos, set price, choose condition. AI auto-generates SEO metadata.'],
                ['icon' => '💳', 'title' => 'Full price payouts', 'text' => 'Your price goes 100% to your bank account. SmooveHub adds its 10% on top — paid by the buyer, not you.'],
                ['icon' => '🚛', 'title' => 'CRS handles delivery', 'text' => 'SmooveHub and CRS coordinate delivery. Just hand off to the driver.'],
            ],
            'pricing' => [
                ['value' => '100%', 'label' => 'Your payout', 'description' => 'Your price goes 100% to your bank. Set it, keep it.', 'tone' => 'green'],
                ['value' => '+10%', 'label' => 'SmooveHub fee', 'description' => 'Added on top of your price — paid by the customer, not you.', 'tone' => 'purple'],
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function sellersPage(): array
    {
        return array_merge($this->sharedLayout(), [
            'sellers' => $this->sellers(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $seller
     * @return array<string, mixed>
     */
    public function sellerStorePage(array $seller): array
    {
        $products = array_values(array_filter(
            $this->products(),
            fn (array $product) => str_contains(strtolower($product['seller']), strtolower(explode(' ', $seller['name'])[0])),
        ));

        if ($products === []) {
            $products = array_slice($this->products(), 0, 4);
        }

        return array_merge($this->sharedLayout(), [
            'seller' => $seller,
            'products' => $products,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function citiesPage(): array
    {
        return array_merge($this->sharedLayout(), [
            'cities' => $this->cities(),
            'featured_city' => $this->cities()[0],
            'products' => array_slice($this->products(), 0, 8),
        ]);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findCity(string $slug): ?array
    {
        foreach ($this->cities() as $city) {
            if ($city['slug'] === $slug) {
                return $city;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    public function cityShowPage(array $city): array
    {
        return array_merge($this->sharedLayout(), [
            'city' => $city,
            'cities' => $this->cities(),
            'products' => array_slice($this->products(), 0, 8),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function blogPage(): array
    {
        return array_merge($this->sharedLayout(), [
            'posts' => $this->blogPosts(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function privacyPage(): array
    {
        return array_merge($this->sharedLayout(), [
            'sections' => $this->privacySections(),
            'sidebar' => $this->legalSidebar('terms'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function termsPage(): array
    {
        return array_merge($this->sharedLayout(), [
            'sections' => $this->termsSections(),
            'sidebar' => $this->legalSidebar('privacy'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function contactPage(): array
    {
        return array_merge($this->sharedLayout(), [
            'hero_badge' => '📞 We respond within 2 business hours',
            'hero_subtitle' => 'Our support team is ready to help with orders, deliveries, seller questions, driver applications, and anything else. Reach us any way you prefer.',
            'phone' => '(877) 385-2919',
            'phone_href' => 'tel:8773852919',
            'support_email' => 'support@smoovehub.com',
            'crs_email' => 'love@competitiverelocation.com',
            'crs_email_alt' => 'lamar@competitiverelocation.com',
            'driver_portal_url' => 'https://competitiverelocationcrm.com/auth/login',
            'crs_promo_url' => 'https://competitiverelocationcrm.com',
            'hours_strip' => [
                '🕐 Mon – Sat · 8:00 AM – 8:00 PM CT',
                '📍 Serving customers nationwide',
                '⚡ Avg. response under 2 hours',
                '📧 24/7 email support',
            ],
            'contact_cards' => $this->contactCards(),
            'business_hours' => [
                ['label' => 'Monday – Friday', 'value' => '8AM – 8PM', 'tone' => 'open'],
                ['label' => 'Saturday', 'value' => '8AM – 8PM', 'tone' => 'open'],
                ['label' => 'Sunday', 'value' => 'Closed', 'tone' => 'closed'],
            ],
            'quick_actions' => [
                ['icon' => '📦', 'label' => 'Get a delivery quote', 'route' => 'site.quote', 'tone' => 'blue'],
                ['icon' => '🚛', 'label' => 'Apply to become a driver', 'route' => 'site.drive', 'tone' => 'blue'],
                ['icon' => '🏪', 'label' => 'Register as a seller', 'route' => 'site.sell', 'tone' => 'orange'],
                ['icon' => '🔒', 'label' => 'Privacy policy', 'route' => 'site.privacy', 'tone' => 'blue'],
                ['icon' => '📄', 'label' => 'Terms of service', 'route' => 'site.terms', 'tone' => 'blue'],
            ],
            'role_options' => [
                '' => 'Select your role…',
                'customer' => 'Customer — order or delivery question',
                'seller' => 'Seller — account, listing, or payout question',
                'driver' => 'Driver applicant — application status question',
                'business' => 'Business — bulk or wholesale inquiry',
                'press' => 'Press / media inquiry',
                'other' => 'Other',
            ],
            'faqs' => $this->contactFaqs(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function quotePage(): array
    {
        return array_merge($this->sharedLayout(), [
            'hero_badge' => '🚛 Powered by Competitive Relocation Service',
            'hero_subtitle' => 'Enter your access details — door dimensions, stairs, and elevator — then we connect you directly to CRS for live pricing on your furniture delivery.',
            'steps' => [
                ['number' => 1, 'text' => 'Enter access details', 'highlight' => false],
                ['number' => 2, 'text' => 'See your estimate', 'highlight' => false],
                ['number' => 3, 'text' => 'CRS live quote — final confirmed price', 'highlight' => true],
            ],
            'crs_base_url' => 'https://www.competitiverelocation.com/services/?services=delivery',
            'crs_phone' => '(877) 385-2919',
            'crs_phone_href' => 'tel:8773852919',
            'crs_email' => 'info@competitiverelocation.com',
            'surcharges' => $this->quoteSurcharges(),
            'distance_rules' => $this->quoteDistanceRules(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function drivePage(): array
    {
        return array_merge($this->sharedLayout(), [
            'hero_badge' => '🚛 Driver Partner Network',
            'hero_subtitle' => 'Join 500+ drivers earning more with Competitive Relocation Service. Enterprise-level onboarding, weekly payouts, real-time Loadboard.',
            'crs_login_url' => 'https://competitiverelocationcrm.com/auth/login',
            'benefit_items' => [
                [
                    'icon' => '💰',
                    'tone' => 'blue',
                    'title' => 'Weekly direct payouts',
                    'text' => 'ACH, Stripe Connect, Zelle, Venmo supported.',
                ],
                [
                    'icon' => '📦',
                    'tone' => 'orange',
                    'title' => 'Real-time Loadboard',
                    'text' => 'Claim paid jobs the moment they post.',
                ],
                [
                    'icon' => '⚡',
                    'tone' => 'green',
                    'title' => '48-hour approval',
                    'text' => 'MC/DOT review within 1–2 business days.',
                ],
            ],
            'vehicles' => [
                ['value' => 'car', 'icon' => '🚗', 'label' => 'Car / SUV', 'subtext' => 'Small items only', 'wide' => false],
                ['value' => 'van', 'icon' => '🚐', 'label' => 'Cargo Van', 'subtext' => 'Mid-size furniture', 'wide' => false],
                ['value' => 'pickup', 'icon' => '🛻', 'label' => 'Pickup Truck', 'subtext' => 'Larger items', 'wide' => false],
                ['value' => 'boxtruck', 'icon' => '🚛', 'label' => 'Box Truck', 'subtext' => 'Full loads · DOT req.', 'wide' => false],
                ['value' => 'semitruck', 'icon' => '🚚', 'label' => 'Semi / 18-Wheeler', 'subtext' => 'Commercial loads · DOT + MC required', 'wide' => true],
            ],
            'payout_methods' => [
                ['value' => 'ach', 'label' => '🏦 ACH'],
                ['value' => 'stripe', 'label' => '💳 Stripe'],
                ['value' => 'zelle', 'label' => '⚡ Zelle'],
                ['value' => 'venmo', 'label' => '💸 Venmo'],
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function designRoomPage(): array
    {
        return array_merge($this->sharedLayout(), [
            'hero_badge' => '✨ AI-Powered · Camera Ready · Shop Direct',
            'hero_subtitle' => "Upload a photo or video of your room, choose furniture from SmooveHub's catalog, and see it placed in your space. Get AI-powered suggestions for matching pieces — all shoppable instantly.",
            'hero_features' => ['📷 Photo Upload', '🎥 Video Capture', '📐 Real Measurements', '🤖 AI Suggestions', '🛒 Shop Instantly'],
            'room_types' => [
                ['key' => 'living', 'icon' => '🛋️', 'label' => 'Living Room'],
                ['key' => 'bedroom', 'icon' => '🛏️', 'label' => 'Bedroom'],
                ['key' => 'dining', 'icon' => '🍽️', 'label' => 'Dining Room'],
                ['key' => 'office', 'icon' => '💼', 'label' => 'Home Office'],
                ['key' => 'outdoor', 'icon' => '🌿', 'label' => 'Outdoor'],
            ],
            'style_guide' => [
                'aesthetic' => 'Japandi-Modern',
                'accents' => ['🪵 Natural Wood', '🌿 Greenery', '🕯️ Warm Lighting', '🎋 Neutral Tones'],
            ],
            'catalog_categories' => ['Living', 'Dining', 'Bedroom', 'Office', 'Commercial'],
            'shop_url' => route('site.shop'),
            'inventory' => $this->designRoomInventory(),
            'ai_suggestions' => [
                'living' => [
                    ['id' => 'INV-006', 'label' => '🛋️ Anchor Piece', 'note' => 'Perfect anchor for your living room', 'tone' => 'pink'],
                    ['id' => 'INV-EX1', 'label' => '✨ Style Upgrade', 'note' => 'Earthy balance piece', 'tone' => 'amber'],
                    ['id' => 'INV-001', 'label' => '💡 Complete the Look', 'note' => 'Japandi aesthetic match', 'tone' => 'green'],
                ],
                'dining' => [
                    ['id' => 'INV-001', 'label' => '🍽️ Anchor Piece', 'note' => 'Perfect dining table', 'tone' => 'pink'],
                    ['id' => 'INV-EX1', 'label' => '🪑 Side Seating', 'note' => 'Accent chairs for the corner', 'tone' => 'amber'],
                    ['id' => 'INV-009', 'label' => '🗃️ Storage', 'note' => 'Cabinet for dining essentials', 'tone' => 'green'],
                ],
                'bedroom' => [
                    ['id' => 'INV-004', 'label' => '🛏️ Anchor Piece', 'note' => 'King bed frame to center the room', 'tone' => 'pink'],
                    ['id' => 'INV-009', 'label' => '📦 Storage', 'note' => 'Lateral cabinet for bedroom storage', 'tone' => 'amber'],
                    ['id' => 'INV-EX1', 'label' => '🌿 Accent', 'note' => 'Rattan chair for reading nook', 'tone' => 'green'],
                ],
                'office' => [
                    ['id' => 'INV-003', 'label' => '💻 Anchor Piece', 'note' => 'Standing desk for productivity', 'tone' => 'pink'],
                    ['id' => 'INV-008', 'label' => '🪑 Seating', 'note' => 'Ergonomic chair — BIFMA certified', 'tone' => 'green'],
                    ['id' => 'INV-009', 'label' => '🗃️ Storage', 'note' => 'Filing cabinet for organization', 'tone' => 'amber'],
                ],
                'outdoor' => [
                    ['id' => 'INV-EX1', 'label' => '🌿 Accent Seating', 'note' => 'Rattan chair — weather-friendly', 'tone' => 'green'],
                    ['id' => 'INV-001', 'label' => '🍽️ Dining', 'note' => 'Oak table for outdoor dining', 'tone' => 'pink'],
                ],
            ],
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function designRoomInventory(): array
    {
        return [
            [
                'id' => 'INV-001', 'sku' => 'SH-0001', 'name' => 'Japandi Solid Oak Dining Table', 'brand' => 'LuxCraft',
                'category' => 'Dining', 'price' => 1249, 'orig' => 1780, 'seller' => 'HomeStyle Co.', 'condition' => 'Brand New',
                'icon' => '🪑', 'material' => 'Solid Oak', 'color' => 'Natural Walnut', 'finish' => 'Matte lacquer',
                'dims' => '72×36×30"', 'weight' => '95 lbs', 'warranty' => '2 years', 'assembly' => '45 min',
                'description' => 'Premium solid oak dining table with Japandi-inspired clean lines. Anti-scratch lacquer finish. Seats 6 comfortably.',
                'colors' => ['Natural Walnut', 'Dark Walnut', 'Whitewash'],
                'colorHex' => ['#8B6914', '#4A2C0A', '#E8DDD0'],
                'tags' => ['dining', 'solid wood', 'japandi', 'modern'],
                'fits' => ['dining', 'living'],
                'shop_product_id' => 1,
            ],
            [
                'id' => 'INV-006', 'sku' => 'SH-0006', 'name' => 'Modular Sectional Sofa — Grey', 'brand' => 'LuxCraft',
                'category' => 'Living', 'price' => 2127, 'orig' => 3100, 'seller' => 'LuxLiving', 'condition' => 'Brand New',
                'icon' => '🛋️', 'material' => 'Performance Fabric', 'color' => 'Charcoal Grey', 'finish' => 'N/A',
                'dims' => '118×90×34"', 'weight' => '210 lbs', 'warranty' => '3 years', 'assembly' => '90 min',
                'description' => '5-piece modular sectional with chaise. Stain-resistant performance fabric. Cup holders included. Left or right configuration.',
                'colors' => ['Charcoal Grey', 'Slate Blue', 'Ivory White', 'Forest Green'],
                'colorHex' => ['#4B5563', '#4A6FA5', '#F8F4EE', '#2D6A4F'],
                'tags' => ['sofa', 'sectional', 'living room', 'modular'],
                'fits' => ['living'],
            ],
            [
                'id' => 'INV-002', 'sku' => 'SH-0002', 'name' => 'Velvet L-Shape Sectional', 'brand' => 'ComfyHome',
                'category' => 'Living', 'price' => 1568, 'orig' => 2200, 'seller' => 'LuxLiving', 'condition' => 'Brand New',
                'icon' => '🛋️', 'material' => 'Velvet + Solid Wood Frame', 'color' => 'Charcoal', 'finish' => 'N/A',
                'dims' => '110×85×34"', 'weight' => '185 lbs', 'warranty' => '1 year', 'assembly' => '60 min',
                'description' => 'Luxurious velvet L-shape sectional with high-density foam cushions. Solid eucalyptus wood frame. Reversible chaise.',
                'colors' => ['Charcoal', 'Emerald', 'Dusty Rose', 'Navy Blue', 'Cream', 'Terracotta'],
                'colorHex' => ['#374151', '#065F46', '#F4A8C0', '#1E3A8A', '#FDF9F0', '#C2410C'],
                'tags' => ['sofa', 'velvet', 'luxury', 'living room'],
                'fits' => ['living'],
            ],
            [
                'id' => 'INV-003', 'sku' => 'SH-0003', 'name' => 'Executive Standing Desk Pro', 'brand' => 'WorkWell',
                'category' => 'Office', 'price' => 527, 'orig' => 800, 'seller' => 'BizFurniture', 'condition' => 'Brand New',
                'icon' => '💻', 'material' => 'Steel + MDF', 'color' => 'Matte Black', 'finish' => 'Powder coat',
                'dims' => '60×30×48"', 'weight' => '58 lbs', 'warranty' => '3 years', 'assembly' => 'No assembly',
                'description' => 'Electric height-adjustable standing desk. Programmable memory settings (4 presets). Anti-collision sensor. USB-A/C charging ports built in.',
                'colors' => ['Matte Black', 'White', 'Walnut + Black', 'Walnut + White'],
                'colorHex' => ['#1F2937', '#FFFFFF', '#1F2937', '#FFFFFF'],
                'tags' => ['office', 'desk', 'standing desk', 'ergonomic'],
                'fits' => ['office'],
                'shop_product_id' => 7,
            ],
            [
                'id' => 'INV-004', 'sku' => 'SH-0004', 'name' => 'King Bed Frame — Linen Upholstered', 'brand' => 'RestWell',
                'category' => 'Bedroom', 'price' => 918, 'orig' => 1300, 'seller' => 'HomeStyle Co.', 'condition' => 'Brand New',
                'icon' => '🛏️', 'material' => 'Linen + Solid Wood + MDF', 'color' => 'Ivory Linen', 'finish' => 'N/A',
                'dims' => '82×76×50"', 'weight' => '120 lbs', 'warranty' => '2 years', 'assembly' => '40 min',
                'description' => 'King-size upholstered bed frame with tall tufted headboard. Solid wood slats included. Center support legs. No box spring required.',
                'colors' => ['Ivory Linen', 'Charcoal Linen', 'Sage Green', 'Dusty Blue'],
                'colorHex' => ['#F5F0E8', '#374151', '#6B7C6E', '#7BA7C4'],
                'tags' => ['bedroom', 'bed frame', 'king', 'upholstered'],
                'fits' => ['bedroom'],
                'shop_product_id' => 8,
            ],
            [
                'id' => 'INV-008', 'sku' => 'SH-0008', 'name' => 'Ergonomic Mesh Executive Chair', 'brand' => 'WorkWell',
                'category' => 'Office', 'price' => 484, 'orig' => 700, 'seller' => 'BizFurniture', 'condition' => 'Floor Model',
                'icon' => '🪑', 'material' => 'Mesh + Aluminum', 'color' => 'Black/Silver', 'finish' => 'N/A',
                'dims' => '26×26×45"', 'weight' => '32 lbs', 'warranty' => '2 years', 'assembly' => '15 min',
                'description' => 'Premium ergonomic mesh office chair. Lumbar support, adjustable armrests, headrest. 360° swivel. BIFMA certified.',
                'colors' => ['Black/Silver', 'White/Chrome', 'Grey/Black'],
                'colorHex' => ['#1F2937', '#E5E7EB', '#6B7280'],
                'tags' => ['office', 'chair', 'ergonomic', 'mesh'],
                'fits' => ['office'],
                'shop_product_id' => 9,
            ],
            [
                'id' => 'INV-009', 'sku' => 'SH-0009', 'name' => '4-Drawer Lateral File Cabinet', 'brand' => 'WorkWell',
                'category' => 'Office', 'price' => 663, 'orig' => 950, 'seller' => 'BizFurniture', 'condition' => 'Brand New',
                'icon' => '🗃️', 'material' => 'Cold-rolled steel', 'color' => 'Gunmetal', 'finish' => 'Powder coat',
                'dims' => '36×18×52"', 'weight' => '145 lbs', 'warranty' => '5 years', 'assembly' => 'No assembly',
                'description' => '4-drawer lateral file cabinet with anti-tilt mechanism. Central locking system. Full-extension ball-bearing drawers.',
                'colors' => ['Gunmetal', 'Warm White', 'Matte Black'],
                'colorHex' => ['#6B7280', '#F9FAFB', '#1F2937'],
                'tags' => ['office', 'storage', 'filing cabinet'],
                'fits' => ['office', 'bedroom'],
            ],
            [
                'id' => 'INV-EX1', 'sku' => 'SH-0010', 'name' => 'Biophilic Rattan Accent Chair', 'brand' => 'HomeStyle',
                'category' => 'Living', 'price' => 449, 'orig' => 620, 'seller' => 'HomeStyle Co.', 'condition' => 'Brand New',
                'icon' => '🌿', 'material' => 'Rattan + Solid Teak Frame', 'color' => 'Natural Rattan', 'finish' => 'Natural',
                'dims' => '28×30×32"', 'weight' => '18 lbs', 'warranty' => '1 year', 'assembly' => '20 min',
                'description' => 'Handwoven rattan accent chair with solid teak legs. Removable cushion in natural linen. Lightweight, perfect for any corner.',
                'colors' => ['Natural Rattan', 'Bleached White', 'Dark Teak'],
                'colorHex' => ['#C49A45', '#F0E6D3', '#3D2B1F'],
                'tags' => ['accent', 'rattan', 'living room', 'natural'],
                'fits' => ['living', 'bedroom', 'outdoor'],
                'shop_product_id' => 12,
            ],
            [
                'id' => 'INV-EX2', 'sku' => 'SH-0011', 'name' => 'Plush Velvet Sectional Sofa', 'brand' => 'LuxLiving',
                'category' => 'Living', 'price' => 2190, 'orig' => 3100, 'seller' => 'LuxLiving', 'condition' => 'Floor Model',
                'icon' => '🛋️', 'material' => 'Velvet + Kiln-dried Hardwood', 'color' => 'Midnight Blue', 'finish' => 'N/A',
                'dims' => '120×85×33"', 'weight' => '195 lbs', 'warranty' => '2 years', 'assembly' => '75 min',
                'description' => 'Ultra-luxurious plush velvet sectional with kiln-dried hardwood frame. High-resilience foam. Comes with matching throw pillows.',
                'colors' => ['Midnight Blue', 'Dusty Mauve', 'Forest Green', 'Cognac Brown'],
                'colorHex' => ['#1E3A8A', '#9F7DA0', '#14532D', '#92400E'],
                'tags' => ['sofa', 'velvet', 'luxury', 'plush'],
                'fits' => ['living'],
                'shop_product_id' => 3,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function designMePage(): array
    {
        return array_merge($this->sharedLayout(), [
            'hero_badge' => '🔨 Custom Build · Handcrafted · Your Specs',
            'hero_subtitle' => 'Configure your custom furniture piece from scratch — choose the type, material, dimensions, colors, and features. Get an instant quote and submit your order to our verified craftsmen network.',
            'hero_stats' => [
                ['value' => '180+', 'label' => 'Verified Craftsmen'],
                ['value' => '14–28', 'label' => 'Day Build Time'],
                ['value' => '100%', 'label' => 'Custom to Spec'],
                ['value' => 'CRS', 'label' => 'White-glove Delivery'],
            ],
            'types' => [
                ['key' => 'sofa', 'icon' => '🛋️', 'label' => 'Sofa / Sectional'],
                ['key' => 'table', 'icon' => '🪑', 'label' => 'Dining Table'],
                ['key' => 'bed', 'icon' => '🛏️', 'label' => 'Bed Frame'],
                ['key' => 'desk', 'icon' => '💻', 'label' => 'Desk / Workstation'],
                ['key' => 'chair', 'icon' => '🪑', 'label' => 'Chair / Accent'],
                ['key' => 'shelf', 'icon' => '📚', 'label' => 'Bookshelf / Storage'],
                ['key' => 'cabinet', 'icon' => '🗄️', 'label' => 'Cabinet / Wardrobe'],
                ['key' => 'outdoor', 'icon' => '🌿', 'label' => 'Outdoor / Patio'],
            ],
            'materials' => [
                ['key' => 'solid-oak', 'label' => 'Solid Oak', 'desc' => 'Premium · Durable · Classic', 'price_label' => '+$0 base', 'price_tone' => 'orange', 'swatch' => 'linear-gradient(135deg,#8B6914,#C49A45)'],
                ['key' => 'walnut', 'label' => 'Black Walnut', 'desc' => 'Luxury · Dark · Rich grain', 'price_label' => '+$220', 'price_tone' => 'orange', 'swatch' => 'linear-gradient(135deg,#4A2C0A,#7A4F2A)'],
                ['key' => 'pine', 'label' => 'Pine Wood', 'desc' => 'Lightweight · Budget · Natural', 'price_label' => '−$80', 'price_tone' => 'green', 'swatch' => 'linear-gradient(135deg,#D4A96A,#E8C882)'],
                ['key' => 'steel', 'label' => 'Steel Frame', 'desc' => 'Industrial · Modern · Strong', 'price_label' => '+$140', 'price_tone' => 'orange', 'swatch' => 'linear-gradient(135deg,#374151,#6B7280)'],
                ['key' => 'velvet', 'label' => 'Velvet Fabric', 'desc' => 'Luxe · Soft · Bold colors', 'price_label' => '+$180', 'price_tone' => 'orange', 'swatch' => 'linear-gradient(135deg,#4C1D95,#7C3AED)'],
                ['key' => 'rattan', 'label' => 'Rattan / Wicker', 'desc' => 'Organic · Boho · Lightweight', 'price_label' => '−$60', 'price_tone' => 'green', 'swatch' => 'linear-gradient(135deg,#C49A45,#E8C882)'],
            ],
            'finishes' => ['Matte', 'Gloss', 'Satin', 'Oiled', 'Waxed', 'Raw / Unfinished'],
            'sources' => [
                'Instagram / Social Media',
                'Google Search',
                'Friend / Referral',
                'SmooveHub Marketplace',
                'Other',
            ],
            'craftsmen' => [
                ['initial' => 'R', 'name' => "Ramon's Wood Co.", 'meta' => '⭐ 4.9 · Oak specialist · Dallas, TX', 'badge' => 'Top Match', 'badge_tone' => 'green', 'avatar' => 'orange'],
                ['initial' => 'L', 'name' => 'LuxBuild Atelier', 'meta' => '⭐ 4.8 · Upholstery · Houston, TX', 'badge' => '+2 more', 'badge_tone' => 'purple', 'avatar' => 'purple'],
            ],
            'engine' => [
                'presets' => [
                    'sofa' => [['n' => 'Love Seat', 'w' => 60, 'd' => 35, 'h' => 33], ['n' => '3-Seater', 'w' => 84, 'd' => 36, 'h' => 34], ['n' => 'Sectional', 'w' => 118, 'd' => 90, 'h' => 34]],
                    'table' => [['n' => '2-Top', 'w' => 36, 'd' => 30, 'h' => 30], ['n' => '4-Top', 'w' => 60, 'd' => 36, 'h' => 30], ['n' => '6-Top', 'w' => 72, 'd' => 36, 'h' => 30], ['n' => '8-Top', 'w' => 96, 'd' => 42, 'h' => 30]],
                    'bed' => [['n' => 'Twin', 'w' => 39, 'd' => 75, 'h' => 14], ['n' => 'Full', 'w' => 54, 'd' => 75, 'h' => 14], ['n' => 'Queen', 'w' => 60, 'd' => 80, 'h' => 14], ['n' => 'King', 'w' => 76, 'd' => 80, 'h' => 14]],
                    'desk' => [['n' => 'Compact', 'w' => 48, 'd' => 24, 'h' => 29], ['n' => 'Standard', 'w' => 60, 'd' => 30, 'h' => 30], ['n' => 'L-Shape', 'w' => 84, 'd' => 60, 'h' => 30]],
                    'chair' => [['n' => 'Accent', 'w' => 28, 'd' => 30, 'h' => 32], ['n' => 'Armchair', 'w' => 32, 'd' => 34, 'h' => 34], ['n' => 'Dining', 'w' => 18, 'd' => 18, 'h' => 34]],
                    'shelf' => [['n' => '3-Shelf', 'w' => 36, 'd' => 12, 'h' => 48], ['n' => '5-Shelf', 'w' => 36, 'd' => 12, 'h' => 72], ['n' => 'Wide', 'w' => 60, 'd' => 14, 'h' => 72]],
                    'cabinet' => [['n' => '2-Door', 'w' => 36, 'd' => 18, 'h' => 72], ['n' => '4-Door', 'w' => 60, 'd' => 18, 'h' => 72], ['n' => 'Wardrobe', 'w' => 72, 'd' => 24, 'h' => 84]],
                    'outdoor' => [['n' => 'Loveseat', 'w' => 52, 'd' => 32, 'h' => 33], ['n' => 'Dining Set', 'w' => 72, 'd' => 36, 'h' => 30], ['n' => 'Lounger', 'w' => 75, 'd' => 30, 'h' => 16]],
                ],
                'matMod' => ['solid-oak' => 0, 'walnut' => 220, 'pine' => -80, 'steel' => 140, 'velvet' => 180, 'rattan' => -60],
                'base' => ['sofa' => 1200, 'table' => 800, 'bed' => 900, 'desk' => 600, 'chair' => 400, 'shelf' => 350, 'cabinet' => 700, 'outdoor' => 550],
                'colors' => [
                    ['name' => 'Natural Walnut', 'hex' => '#8B6914'],
                    ['name' => 'Dark Walnut', 'hex' => '#4A2C0A'],
                    ['name' => 'Whitewash', 'hex' => '#E8DDD0'],
                    ['name' => 'Matte Black', 'hex' => '#1F2937'],
                    ['name' => 'Pure White', 'hex' => '#F9FAFB'],
                    ['name' => 'Charcoal Grey', 'hex' => '#4B5563'],
                    ['name' => 'Navy Blue', 'hex' => '#1E3A8A'],
                    ['name' => 'Forest Green', 'hex' => '#14532D'],
                    ['name' => 'Burgundy', 'hex' => '#7F1D1D'],
                    ['name' => 'Dusty Rose', 'hex' => '#F9A8D4'],
                    ['name' => 'Cognac Brown', 'hex' => '#92400E'],
                    ['name' => 'Ivory Linen', 'hex' => '#F5F0E8'],
                    ['name' => 'Sage Green', 'hex' => '#6B7C6E'],
                    ['name' => 'Terracotta', 'hex' => '#C2410C'],
                    ['name' => 'Sand Beige', 'hex' => '#D4B896'],
                    ['name' => 'Midnight', 'hex' => '#0F172A'],
                ],
                'features' => [
                    'sofa' => [['id' => 'usb', 'icon' => '🔌', 'label' => 'USB Charging Ports', 'price' => 80], ['id' => 'storage', 'icon' => '📦', 'label' => 'Hidden Storage', 'price' => 120], ['id' => 'recline', 'icon' => '↩️', 'label' => 'Recline Mechanism', 'price' => 200], ['id' => 'cupholders', 'icon' => '☕', 'label' => 'Cup Holders', 'price' => 60], ['id' => 'extra-cushion', 'icon' => '🛋️', 'label' => 'Extra-thick Cushions', 'price' => 90], ['id' => 'waterproof', 'icon' => '💧', 'label' => 'Waterproof Fabric', 'price' => 110]],
                    'table' => [['id' => 'extension', 'icon' => '↔️', 'label' => 'Extension Leaf', 'price' => 160], ['id' => 'storage', 'icon' => '📦', 'label' => 'Under-table Storage', 'price' => 100], ['id' => 'lazy-susan', 'icon' => '🌀', 'label' => 'Lazy Susan Center', 'price' => 80], ['id' => 'anti-scratch', 'icon' => '🛡️', 'label' => 'Anti-scratch Coat', 'price' => 60], ['id' => 'glass-inlay', 'icon' => '🔲', 'label' => 'Glass Inlay Top', 'price' => 180], ['id' => 'casters', 'icon' => '🎡', 'label' => 'Locking Casters', 'price' => 70]],
                    'bed' => [['id' => 'storage', 'icon' => '📦', 'label' => 'Under-bed Drawers', 'price' => 220], ['id' => 'usb', 'icon' => '🔌', 'label' => 'Headboard USB Ports', 'price' => 90], ['id' => 'led', 'icon' => '💡', 'label' => 'LED Ambience Lighting', 'price' => 140], ['id' => 'upholstered', 'icon' => '🧵', 'label' => 'Full Upholstered Frame', 'price' => 180], ['id' => 'platform', 'icon' => '📐', 'label' => 'Platform (no box spring)', 'price' => 0], ['id' => 'adjustable', 'icon' => '⚙️', 'label' => 'Adjustable Headboard', 'price' => 160]],
                    'desk' => [['id' => 'electric', 'icon' => '⚡', 'label' => 'Electric Height Adjust', 'price' => 280], ['id' => 'cable', 'icon' => '🔌', 'label' => 'Cable Management', 'price' => 70], ['id' => 'usb', 'icon' => '🔋', 'label' => 'USB-C/A Hub Built-in', 'price' => 100], ['id' => 'monitor-arm', 'icon' => '🖥️', 'label' => 'Monitor Arm Mount', 'price' => 90], ['id' => 'drawer', 'icon' => '📋', 'label' => 'Side Pedestal Drawer', 'price' => 140], ['id' => 'glass', 'icon' => '🔲', 'label' => 'Tempered Glass Top', 'price' => 160]],
                    'chair' => [['id' => 'ergonomic', 'icon' => '🦴', 'label' => 'Ergonomic Lumbar', 'price' => 100], ['id' => 'armrests', 'icon' => '🤲', 'label' => 'Adjustable Armrests', 'price' => 70], ['id' => 'swivel', 'icon' => '🌀', 'label' => '360° Swivel Base', 'price' => 80], ['id' => 'ottoman', 'icon' => '🪑', 'label' => 'Matching Ottoman', 'price' => 160], ['id' => 'leather', 'icon' => '🧴', 'label' => 'Leather Upholstery Upgrade', 'price' => 200], ['id' => 'recline', 'icon' => '↩️', 'label' => 'Reclining Mechanism', 'price' => 180]],
                    'shelf' => [['id' => 'doors', 'icon' => '🚪', 'label' => 'Glass Cabinet Doors', 'price' => 140], ['id' => 'led', 'icon' => '💡', 'label' => 'LED Strip Lighting', 'price' => 80], ['id' => 'ladder', 'icon' => '🪜', 'label' => 'Rolling Library Ladder', 'price' => 220], ['id' => 'adjustable', 'icon' => '⚙️', 'label' => 'Adjustable Shelves', 'price' => 0], ['id' => 'cable', 'icon' => '🔌', 'label' => 'Cable Routing', 'price' => 50], ['id' => 'wall-anchor', 'icon' => '⚓', 'label' => 'Anti-tip Wall Anchor', 'price' => 30]],
                    'cabinet' => [['id' => 'soft-close', 'icon' => '🤫', 'label' => 'Soft-close Hinges', 'price' => 60], ['id' => 'led', 'icon' => '💡', 'label' => 'Interior LED Lighting', 'price' => 90], ['id' => 'lock', 'icon' => '🔒', 'label' => 'Key Lock System', 'price' => 80], ['id' => 'mirror', 'icon' => '🪞', 'label' => 'Full-length Mirror Door', 'price' => 150], ['id' => 'pull-out', 'icon' => '↔️', 'label' => 'Pull-out Shoe Racks', 'price' => 120], ['id' => 'sensor', 'icon' => '✨', 'label' => 'Sensor-activated Light', 'price' => 100]],
                    'outdoor' => [['id' => 'weatherproof', 'icon' => '🌧️', 'label' => 'All-weather Coating', 'price' => 80], ['id' => 'cushions', 'icon' => '🛋️', 'label' => 'UV-resistant Cushions', 'price' => 120], ['id' => 'umbrella', 'icon' => '☂️', 'label' => 'Umbrella Hole & Base', 'price' => 90], ['id' => 'powder', 'icon' => '⚗️', 'label' => 'Powder-coat Finish', 'price' => 70], ['id' => 'anti-rust', 'icon' => '🛡️', 'label' => 'Anti-rust Treatment', 'price' => 60], ['id' => 'folding', 'icon' => '📐', 'label' => 'Folding / Stackable', 'price' => 40]],
                ],
            ],
            'shop_url' => route('site.shop'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function accountPage(): array
    {
        return $this->sharedLayout();
    }

    /**
     * @return array<string, mixed>
     */
    public function dashboardPage(): array
    {
        return [
            'nav_groups' => [
                [
                    'label' => 'Store',
                    'items' => [
                        ['key' => 'overview', 'label' => 'Overview'],
                        ['key' => 'products', 'label' => 'Products'],
                        ['key' => 'orders', 'label' => 'Orders'],
                        ['key' => 'payouts', 'label' => 'Payouts'],
                        ['key' => 'vouchers', 'label' => 'Vouchers'],
                        ['key' => 'settings', 'label' => 'Settings'],
                    ],
                ],
                [
                    'label' => 'Sourcing',
                    'items' => [
                        ['key' => 'distcatalog', 'label' => 'Dist. Catalog', 'badge' => 'Soon'],
                        ['key' => 'myimports', 'label' => 'Imports'],
                        ['key' => 'mypurchases', 'label' => 'Purchases'],
                        ['key' => 'onsiteappts', 'label' => 'Onsite visits'],
                        ['key' => 'openapi', 'label' => 'Open API'],
                    ],
                ],
            ],
            'nav' => [
                ['key' => 'overview', 'label' => 'Overview'],
                ['key' => 'products', 'label' => 'Products'],
                ['key' => 'orders', 'label' => 'Orders'],
                ['key' => 'payouts', 'label' => 'Payouts'],
                ['key' => 'vouchers', 'label' => 'Vouchers'],
                ['key' => 'settings', 'label' => 'Settings'],
                ['key' => 'distcatalog', 'label' => 'Dist. Catalog', 'badge' => 'Soon'],
                ['key' => 'myimports', 'label' => 'Imports'],
                ['key' => 'mypurchases', 'label' => 'Purchases'],
                ['key' => 'onsiteappts', 'label' => 'Onsite visits'],
                ['key' => 'openapi', 'label' => 'Open API'],
            ],
            'stats' => [
                ['label' => 'Revenue (30d)', 'value' => '$12,480'],
                ['label' => 'Orders', 'value' => '28'],
                ['label' => 'Products', 'value' => '14'],
                ['label' => 'Rating', 'value' => '4.9★'],
            ],
            'dist_catalog' => [
                ['sku' => 'ACME-8842', 'name' => 'Modular Sectional — Charcoal', 'wholesale' => 890, 'qty' => 24, 'supplier' => 'ACME Furniture Co.'],
                ['sku' => 'ACME-3310', 'name' => 'Executive Desk — Walnut', 'wholesale' => 420, 'qty' => 18, 'supplier' => 'ACME Furniture Co.'],
                ['sku' => 'ACME-1198', 'name' => 'Conference Table — 10ft', 'wholesale' => 1240, 'qty' => 6, 'supplier' => 'ACME Furniture Co.'],
                ['sku' => 'ACME-5521', 'name' => 'Ergonomic Task Chair', 'wholesale' => 185, 'qty' => 120, 'supplier' => 'ACME Furniture Co.'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function distributePage(): array
    {
        return app(\App\Repositories\Site\Distribute\DistributePortalRepository::class)->pageData();
    }

    /**
     * @return array<string, mixed>
     */
    public function adminPage(): array
    {
        return app(\App\Repositories\Site\Admin\AdminPortalRepository::class)->pageData();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findSeller(string $slug): ?array
    {
        foreach ($this->sellers() as $seller) {
            if ($seller['slug'] === $slug) {
                return $seller;
            }
        }

        return null;
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

    /**
     * @return array<int, array<string, mixed>>
     */
    public function categories(): array
    {
        return [
            ['icon' => '🏢', 'name' => 'Commercial', 'subtitle' => '87 items', 'route' => 'site.shop'],
            ['icon' => '🏠', 'name' => 'Residential', 'subtitle' => '142 items', 'route' => 'site.shop'],
            ['icon' => '🔌', 'name' => 'Appliances', 'subtitle' => '65 items', 'route' => 'site.shop'],
            ['icon' => '✨', 'name' => 'Brand New', 'subtitle' => '+40% markup', 'route' => 'site.shop'],
            ['icon' => '💎', 'name' => 'Thrift Finds', 'subtitle' => '+10% markup', 'route' => 'site.shop'],
            ['icon' => '⚡', 'name' => 'Same-Day', 'subtitle' => 'Select zips', 'route' => 'site.shop'],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function products(): array
    {
        $fromDb = app(\App\Repositories\Catalog\CatalogRepository::class)->storefront();

        return $fromDb !== [] ? $fromDb : $this->fallbackProducts();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fallbackProducts(): array
    {
        return [
            ['id' => 1, 'name' => 'Japandi Solid Oak Dining Table', 'price' => 1249, 'original' => 1780, 'seller' => 'HomeStyle Co.', 'condition' => 'BRAND_NEW', 'category' => 'RESIDENTIAL', 'icon' => '🪑', 'trending' => true, 'same_day' => false],
            ['id' => 2, 'name' => 'Industrial Steel Office Desk Set', 'price' => 849, 'original' => 1200, 'seller' => 'OfficeFirst', 'condition' => 'BRAND_NEW', 'category' => 'COMMERCIAL', 'icon' => '🖥️', 'trending' => false, 'same_day' => true],
            ['id' => 3, 'name' => 'Plush Velvet Sectional Sofa', 'price' => 2190, 'original' => 3100, 'seller' => 'LuxLiving', 'condition' => 'FLOOR_MODEL', 'category' => 'RESIDENTIAL', 'icon' => '🛋️', 'trending' => true, 'same_day' => false],
            ['id' => 4, 'name' => 'Mid-Century Walnut Bookshelf', 'price' => 398, 'original' => 0, 'seller' => 'Thrift Palace', 'condition' => 'THRIFT', 'category' => 'RESIDENTIAL', 'icon' => '📚', 'trending' => false, 'same_day' => false],
            ['id' => 5, 'name' => 'Commercial Restaurant Booth Set', 'price' => 1640, 'original' => 2200, 'seller' => 'BizFurniture', 'condition' => 'BRAND_NEW', 'category' => 'COMMERCIAL', 'icon' => '🍽️', 'trending' => false, 'same_day' => true],
            ['id' => 6, 'name' => 'Smart Refrigerator French Door', 'price' => 2890, 'original' => 3400, 'seller' => 'ApplianceHub TX', 'condition' => 'BRAND_NEW', 'category' => 'APPLIANCES', 'icon' => '🧊', 'trending' => true, 'same_day' => false],
            ['id' => 7, 'name' => 'Adjustable Standing Desk Pro', 'price' => 699, 'original' => 900, 'seller' => 'OfficeFirst', 'condition' => 'REFURBISHED', 'category' => 'COMMERCIAL', 'icon' => '💻', 'trending' => false, 'same_day' => true],
            ['id' => 8, 'name' => 'Minimalist Linen King Bed Frame', 'price' => 1099, 'original' => 1500, 'seller' => 'HomeStyle Co.', 'condition' => 'BRAND_NEW', 'category' => 'RESIDENTIAL', 'icon' => '🛏️', 'trending' => false, 'same_day' => false],
            ['id' => 9, 'name' => 'Italian Leather Executive Chair', 'price' => 599, 'original' => 850, 'seller' => 'BizFurniture', 'condition' => 'FLOOR_MODEL', 'category' => 'COMMERCIAL', 'icon' => '🪑', 'trending' => true, 'same_day' => true],
            ['id' => 10, 'name' => 'Farmhouse Entryway Bench', 'price' => 219, 'original' => 0, 'seller' => 'Thrift Palace', 'condition' => 'THRIFT', 'category' => 'RESIDENTIAL', 'icon' => '🪵', 'trending' => false, 'same_day' => false],
            ['id' => 11, 'name' => 'Stainless Commercial Dishwasher', 'price' => 1880, 'original' => 2400, 'seller' => 'ApplianceHub TX', 'condition' => 'BRAND_NEW', 'category' => 'APPLIANCES', 'icon' => '🍽️', 'trending' => false, 'same_day' => false],
            ['id' => 12, 'name' => 'Biophilic Rattan Accent Chair', 'price' => 449, 'original' => 620, 'seller' => 'HomeStyle Co.', 'condition' => 'BRAND_NEW', 'category' => 'RESIDENTIAL', 'icon' => '🌿', 'trending' => true, 'same_day' => true],
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
    public function shopFilters(): array
    {
        return [
            ['label' => 'Category', 'items' => [
                ['icon' => '🏢', 'label' => 'Commercial', 'filter' => 'COMMERCIAL'],
                ['icon' => '🏠', 'label' => 'Residential', 'filter' => 'RESIDENTIAL'],
                ['icon' => '🔌', 'label' => 'Appliances', 'filter' => 'APPLIANCES'],
            ]],
            ['label' => 'Condition', 'items' => [
                ['icon' => '✨', 'label' => 'Brand New', 'filter' => 'BRAND_NEW'],
                ['icon' => '💎', 'label' => 'Thrift', 'filter' => 'THRIFT'],
                ['icon' => '🏷', 'label' => 'Floor Model', 'filter' => 'FLOOR_MODEL'],
                ['icon' => '♻️', 'label' => 'Refurbished', 'filter' => 'REFURBISHED'],
            ]],
            ['label' => 'Delivery', 'items' => [
                ['icon' => '⚡', 'label' => 'Same-day', 'filter' => 'same_day'],
                ['icon' => '🚚', 'label' => 'Delivery available', 'filter' => 'delivery'],
            ]],
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
            ['question' => 'Where can I find a sofa near me?', 'route' => 'site.shop'],
            ['question' => 'What furniture stores deliver same day?', 'route' => 'site.shop'],
            ['question' => 'How much does furniture delivery cost?', 'route' => 'site.quote'],
            ['question' => 'Where can I buy office chairs in bulk?', 'route' => 'site.shop'],
            ['question' => 'What are trending furniture styles?', 'route' => 'site.shop'],
            ['question' => 'Where can I find thrift furniture near me?', 'route' => 'site.shop'],
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
            ['quote' => 'Bought a full office set for my restaurant. Delivered next day — the driver kept me updated the entire time.', 'name' => 'Maria K.', 'role' => 'Restaurant owner · Dallas, TX', 'initial' => 'M', 'avatar_class' => 'sh-testimonial-avatar-blue'],
            ['quote' => 'The thrift section is gold — got a Restoration Hardware sofa for 40% less than retail.', 'name' => 'James T.', 'role' => 'Interior designer · Miami, FL', 'initial' => 'J', 'avatar_class' => 'sh-testimonial-avatar-amber'],
            ['quote' => 'Price at checkout matched the quote exactly — no surprise fees. That trust sold me.', 'name' => 'Sandra R.', 'role' => 'Homeowner · Chicago, IL', 'initial' => 'S', 'avatar_class' => 'sh-testimonial-avatar-green'],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function sellers(): array
    {
        return $this->mergeLiveSellers($this->prototypeSellers());
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function prototypeSellers(): array
    {
        return [
            [
                'slug' => 'homestyle-co',
                'name' => 'HomeStyle Co.',
                'initials' => 'HC',
                'category' => 'Residential',
                'category_tone' => 'residential',
                'btype' => 'Furniture retailer',
                'desc' => 'Premium residential furniture with a focus on Japandi and modern minimalist aesthetics. Serving the DFW area since 2019.',
                'rating' => 4.9,
                'sales' => 142,
                'since' => 2019,
                'email' => 'hello@homestyle.com',
            ],
            [
                'slug' => 'officefirst',
                'name' => 'OfficeFirst',
                'initials' => 'O',
                'category' => 'Commercial',
                'category_tone' => 'commercial',
                'btype' => 'Furniture retailer',
                'desc' => "Dallas's go-to source for commercial office furniture. Ergonomic, affordable, and built for the modern workplace.",
                'rating' => 4.7,
                'sales' => 87,
                'since' => 2020,
                'email' => 'sales@officefirst.com',
            ],
            [
                'slug' => 'luxliving',
                'name' => 'LuxLiving',
                'initials' => 'L',
                'category' => 'Residential',
                'category_tone' => 'residential',
                'btype' => 'Interior designer',
                'desc' => 'Curated luxury furniture for discerning homeowners. Floor models and clearance items at unbeatable prices.',
                'rating' => 4.8,
                'sales' => 63,
                'since' => 2021,
                'email' => 'studio@luxliving.com',
            ],
            [
                'slug' => 'thrift-palace',
                'name' => 'Thrift Palace',
                'initials' => 'TP',
                'category' => 'Mixed',
                'category_tone' => 'mixed',
                'btype' => 'Thrift / consignment store',
                'desc' => "The DFW area's largest thrift furniture destination. New arrivals every week — treasures waiting to be found.",
                'rating' => 4.6,
                'sales' => 204,
                'since' => 2018,
                'email' => 'shop@thriftpalace.com',
            ],
            [
                'slug' => 'bizfurniture',
                'name' => 'BizFurniture',
                'initials' => 'B',
                'category' => 'Commercial',
                'category_tone' => 'commercial',
                'btype' => 'Wholesale / distributor',
                'desc' => 'Wholesale commercial furniture supplier. Restaurant booths, office chairs, hotel furnishings — bulk or single unit.',
                'rating' => 4.8,
                'sales' => 118,
                'since' => 2020,
                'email' => 'sales@bizfurniture.com',
            ],
            [
                'slug' => 'appliancehub-tx',
                'name' => 'ApplianceHub TX',
                'initials' => 'AT',
                'category' => 'Appliances',
                'category_tone' => 'appliances',
                'btype' => 'Wholesale / distributor',
                'desc' => "Texas's #1 appliance marketplace. New, refurbished, and floor-model appliances at up to 60% off retail.",
                'rating' => 4.7,
                'sales' => 93,
                'since' => 2021,
                'email' => 'orders@appliancehubtx.com',
            ],
            [
                'slug' => 'demo-seller',
                'name' => 'Demo Furniture Co.',
                'initials' => 'DF',
                'category' => 'Mixed',
                'category_tone' => 'mixed',
                'btype' => 'Furniture retailer',
                'desc' => 'Demo Furniture Co. is your local furniture destination — quality pieces for every room and every budget.',
                'rating' => 4.9,
                'sales' => 12,
                'since' => 2025,
                'email' => 'demo@smoovehub.com',
            ],
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $directory
     * @return array<int, array<string, mixed>>
     */
    private function mergeLiveSellers(array $directory): array
    {
        $bySlug = [];

        foreach ($directory as $seller) {
            $bySlug[$seller['slug']] = $seller;
        }

        SellerProfile::query()
            ->with('user')
            ->get()
            ->each(function (SellerProfile $profile) use (&$bySlug): void {
                $existing = $bySlug[$profile->store_slug] ?? [];
                $mapped = $this->directorySellerFromProfile($profile);

                if (isset($existing['category_tone'])) {
                    unset($mapped['category'], $mapped['category_tone']);
                }

                $bySlug[$profile->store_slug] = array_merge($existing, $mapped);
            });

        return array_values($bySlug);
    }

    /**
     * @return array<string, mixed>
     */
    private function directorySellerFromProfile(SellerProfile $profile): array
    {
        $type = strtolower((string) $profile->business_type);
        $tone = 'mixed';
        $category = $profile->business_type ?: 'Mixed';

        if (str_contains($type, 'commercial') || str_contains($type, 'office')) {
            $tone = 'commercial';
            $category = 'Commercial';
        } elseif (str_contains($type, 'appliance')) {
            $tone = 'appliances';
            $category = 'Appliances';
        } elseif (str_contains($type, 'residential') || str_contains($type, 'furniture')) {
            $tone = 'residential';
            $category = 'Residential';
        }

        $words = preg_split('/\s+/', trim((string) $profile->store_name)) ?: [];
        $initials = strtoupper(substr($words[0] ?? 'S', 0, 1).(isset($words[1]) ? substr($words[1], 0, 1) : ''));

        return [
            'slug' => $profile->store_slug,
            'name' => $profile->store_name,
            'initials' => $initials,
            'logo_url' => $profile->logo_path ? Storage::disk('public')->url($profile->logo_path) : null,
            'category' => $category,
            'category_tone' => $tone,
            'btype' => $profile->business_type ?: 'Seller',
            'desc' => $profile->description ?: '',
            'rating' => round((float) ($profile->rating_avg ?? 0), 1),
            'sales' => (int) ($profile->review_count ?? 0),
            'since' => $profile->member_since?->year ?? $profile->created_at?->year,
            'email' => $profile->user?->email,
            'phone' => $profile->user?->phone,
            'city' => $profile->city,
            'state' => $profile->state,
            'pickup_zip' => $profile->pickup_zip,
            'street' => $profile->street,
            'verified' => $profile->verification_status === 'verified',
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function cities(): array
    {
        return [
            ['slug' => 'dallas-tx', 'emoji' => '🤠', 'name' => 'Dallas', 'state' => 'TX', 'label' => 'Dallas, TX'],
            ['slug' => 'new-york-ny', 'emoji' => '🗽', 'name' => 'New York', 'state' => 'NY', 'label' => 'New York, NY'],
            ['slug' => 'los-angeles-ca', 'emoji' => '🌴', 'name' => 'Los Angeles', 'state' => 'CA', 'label' => 'Los Angeles, CA'],
            ['slug' => 'chicago-il', 'emoji' => '🌆', 'name' => 'Chicago', 'state' => 'IL', 'label' => 'Chicago, IL'],
            ['slug' => 'houston-tx', 'emoji' => '🚀', 'name' => 'Houston', 'state' => 'TX', 'label' => 'Houston, TX'],
            ['slug' => 'miami-fl', 'emoji' => '☀️', 'name' => 'Miami', 'state' => 'FL', 'label' => 'Miami, FL'],
            ['slug' => 'atlanta-ga', 'emoji' => '🍑', 'name' => 'Atlanta', 'state' => 'GA', 'label' => 'Atlanta, GA'],
            ['slug' => 'phoenix-az', 'emoji' => '🌵', 'name' => 'Phoenix', 'state' => 'AZ', 'label' => 'Phoenix, AZ'],
            ['slug' => 'seattle-wa', 'emoji' => '🌧️', 'name' => 'Seattle', 'state' => 'WA', 'label' => 'Seattle, WA'],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function blogPosts(): array
    {
        return [
            ['icon' => '🪴', 'tag' => 'Trends', 'title' => 'Top 7 Japandi Furniture Trends for 2025', 'excerpt' => 'Clean lines, organic textures, and warm neutrals define the Japandi aesthetic.', 'author' => 'SmooveHub Editorial'],
            ['icon' => '⚡', 'tag' => 'Dallas', 'title' => 'Same-Day Furniture Delivery in Dallas, TX', 'excerpt' => 'Need furniture today? SmooveHub partners with CRS for same-day delivery.', 'author' => 'SmooveHub Editorial'],
            ['icon' => '🍽️', 'tag' => 'Commercial', 'title' => 'Commercial Furniture Buying Guide', 'excerpt' => 'From banquette seating to bar stools — how to outfit your restaurant efficiently.', 'author' => 'SmooveHub Editorial'],
            ['icon' => '💡', 'tag' => 'Tips', 'title' => 'Thrift vs Brand New: A Price Breakdown', 'excerpt' => 'Brand new gets a 40% markup. Thrift gets 10%. Here\'s how to decide.', 'author' => 'SmooveHub Editorial'],
            ['icon' => '✨', 'tag' => 'Trends', 'title' => 'Quiet Luxury: The Furniture Aesthetic of 2025', 'excerpt' => 'High quality, minimal logos, timeless silhouettes — quiet luxury in furnishings.', 'author' => 'SmooveHub Editorial'],
            ['icon' => '🚛', 'tag' => 'Delivery', 'title' => 'How CRS Delivers Your Furniture Faster', 'excerpt' => 'An inside look at the Competitive Relocation Service CRM network.', 'author' => 'SmooveHub Editorial'],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function privacySections(): array
    {
        return [
            ['id' => 'pp-collect', 'title' => 'Information We Collect', 'bullets' => ['Personal: name, email, phone, mailing/delivery address', 'Transaction: Stripe payments, order history, delivery preferences, seller payout banking', 'Seller store: listings, inventory, business name/type, pricing', 'Platform usage: pages visited, search queries, device/browser, anonymized IP']],
            ['id' => 'pp-use', 'title' => 'How We Use Your Information', 'bullets' => ['Fulfill orders (Stripe + CRS)', 'Coordinate deliveries via CRS CRM', 'Send email/SMS notifications', 'Improve platform (AI SEO, trends)', 'Legal compliance']],
            ['id' => 'pp-share', 'title' => 'Data Sharing', 'bullets' => ['We never sell personal data', 'Share only to complete purchases (Stripe), coordinate deliveries (CRS), support/disputes, legal obligations', 'Providers: Stripe, CRS, Cloudinary, SendGrid, Twilio, OpenAI']],
            ['id' => 'pp-security', 'title' => 'Security', 'bullets' => ['AES-256 banking encryption', 'PCI-DSS via Stripe', 'HTTPS everywhere', 'MFA for payout changes', 'Role-based access', 'Webhook signature verification']],
            ['id' => 'pp-rights', 'title' => 'Your Rights & Responsibilities', 'bullets' => ['Use responsibly and provide accurate info', 'Comply with laws and policies', 'Protect your credentials', 'Right to access, correct, or delete data']],
            ['id' => 'pp-contact', 'title' => 'Privacy Concerns or Support?', 'bullets' => ['Phone: (877) 385-2919', 'Email: support@smoovehub.com']],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function termsSections(): array
    {
        return [
            ['id' => 'ts-platform', 'title' => 'About the Platform', 'bullets' => ['Multi-vendor marketplace for furniture stores, thrift shops, distributors, independent sellers, delivery providers, and AI tools', 'Listings, purchasing, inventory, CRS delivery, communications']],
            ['id' => 'ts-users', 'title' => 'User Responsibilities', 'bullets' => ['Provide accurate information and secure credentials', 'Comply with applicable laws', 'Sellers: product accuracy, pricing/inventory, timely communication, delivery info, seller policies']],
            ['id' => 'ts-orders', 'title' => 'Orders & Delivery', 'bullets' => ['Subject to stock availability; seller may cancel', 'CRS routing with estimates', 'Accessibility (stairs/elevator/door) affects cost', 'Extra fees for failed/inaccessible deliveries or bad buyer info']],
            ['id' => 'ts-payments', 'title' => 'Payments & Fees', 'bullets' => ['Stripe processing', 'Platform fee disclosed at checkout', 'Seller payout via Stripe Connect weekly', 'In-home delivery FREE at checkout where applicable', 'All fees disclosed before purchase']],
            ['id' => 'ts-suspension', 'title' => 'Suspension & Termination', 'bullets' => ['Fraud or misrepresentation', 'Terms or marketplace policy violation', 'Platform abuse', 'Illegal activity']],
            ['id' => 'ts-liability', 'title' => 'Liability Disclaimer', 'bullets' => ['Independent sellers and providers', 'Not liable for indirect damages, delays, third-party failures', 'Delivery times are estimates', 'Use constitutes consent to Privacy Policy']],
            ['id' => 'ts-contact', 'title' => 'SmooveHub Support', 'bullets' => ['(877) 385-2919', 'support@smoovehub.com', 'https://smoovehub.com']],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function legalSidebar(string $otherPage): array
    {
        return [
            'summary' => [
                'Never sell personal data',
                'Banking encrypted AES-256 + PCI-DSS',
                'Shared only to complete orders & deliveries',
                'Seller inventory remains seller property',
                'Accurate info required from all users',
            ],
            'other_route' => $otherPage === 'privacy' ? 'site.privacy' : 'site.terms',
            'other_label' => $otherPage === 'privacy' ? 'Privacy Policy' : 'Terms of Service',
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function contactCards(): array
    {
        return [
            [
                'icon' => '📞',
                'tone' => 'blue',
                'title' => 'Phone Support',
                'description' => 'Talk directly with our team. Best for urgent delivery issues or order problems needing immediate resolution.',
                'primary' => '(877) 385-2919',
                'secondary' => 'Mon–Sat · 8AM–8PM Central',
                'cta_label' => 'Call Now →',
                'cta_href' => 'tel:8773852919',
            ],
            [
                'icon' => '✉️',
                'tone' => 'orange',
                'title' => 'Email Support',
                'description' => 'Send us a message anytime, 24/7. We respond within 2 business hours during operating hours — often faster.',
                'primary' => 'support@smoovehub.com',
                'secondary' => 'Response within 2 business hours',
                'cta_label' => 'Send Email →',
                'cta_href' => 'mailto:support@smoovehub.com',
            ],
            [
                'icon' => '🚛',
                'tone' => 'green',
                'title' => 'CRS Delivery Support',
                'description' => 'For delivery tracking, driver dispatch, or route issues — contact Competitive Relocation Service directly.',
                'primary' => 'love@competitiverelocation.com',
                'secondary' => 'Also: lamar@competitiverelocation.com',
                'cta_label' => 'Contact CRS →',
                'cta_href' => 'mailto:love@competitiverelocation.com',
            ],
            [
                'icon' => '🧑‍✈️',
                'tone' => 'purple',
                'title' => 'Driver Portal',
                'description' => 'Approved CRS drivers access the Loadboard, delivery jobs, payout history, and route management here.',
                'primary' => 'competitiverelocationcrm.com',
                'secondary' => 'Login with your approved credentials',
                'cta_label' => 'Driver Login →',
                'cta_href' => 'https://competitiverelocationcrm.com/auth/login',
            ],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function contactMethods(): array
    {
        return [
            ['icon' => '📞', 'title' => 'Phone Support', 'detail' => '(877) 385-2919', 'sub' => 'Mon–Sat 8AM–8PM CT · Urgent delivery/order issues'],
            ['icon' => '✉️', 'title' => 'Email Support', 'detail' => 'support@smoovehub.com', 'sub' => '24/7 · Response within 2 business hours'],
            ['icon' => '🚛', 'title' => 'CRS Delivery Support', 'detail' => 'love@competitiverelocation.com', 'sub' => 'Tracking, dispatch, routes'],
            ['icon' => '🚗', 'title' => 'Driver Portal', 'detail' => 'competitiverelocationcrm.com/auth/login', 'sub' => 'Loadboard, jobs, payouts, routes'],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function contactFaqs(): array
    {
        return [
            [
                'q' => 'How do I track my order?',
                'a' => 'Once your order is placed and payment confirmed, it is pushed to the CRS Loadboard. When a driver is assigned you receive an SMS and email with their name, phone, and live ETA. You can also visit SmooveHub and use the Track Order feature in the navigation.',
            ],
            [
                'q' => 'What is the delivery fee and how is it calculated?',
                'a' => 'Delivery is priced via our Competitive Relocation Service (CRS) partner and varies by distance. SmooveHub adds a 3% service fee on top of the CRM base delivery price. Use our free Delivery Quote tool to get an instant itemized estimate before you buy.',
            ],
            [
                'q' => 'How do I become a seller on SmooveHub?',
                'a' => 'Click "Sell" in the navigation or visit the Sell page and click "Create Seller Account." The signup takes about 5 minutes — provide your personal info, business details, and payout banking information. Your account is reviewed and activated within 24 hours.',
            ],
            [
                'q' => 'When do sellers get paid?',
                'a' => 'Sellers receive 88% of the listing price paid out every Friday via Stripe Connect. Platform fee is 9% and delivery service coordination fee is 3%. All fees are clearly disclosed at checkout.',
            ],
            [
                'q' => 'How do I apply to become a driver / carrier?',
                'a' => 'Click "Drive" in the navigation and complete the driver application. You\'ll need MC/DOT numbers, insurance documents, and banking info for payouts. CRS reviews applications within 48 hours and sends login credentials to the Driver Portal.',
            ],
            [
                'q' => 'What happens if my furniture is damaged during delivery?',
                'a' => 'All deliveries include basic liability through Competitive Relocation Service. Claims must be filed within 48 hours of delivery by emailing love@competitiverelocation.com or calling 877-385-2919. Please photograph the damage immediately upon receipt.',
            ],
            [
                'q' => 'Can I cancel or return my order?',
                'a' => 'Orders can be cancelled free of charge up to 24 hours before the scheduled delivery. Within 24 hours, a $50 cancellation fee applies. Brand new items can be returned within 30 days in original condition. Thrift, floor model, and refurbished items are sold as-is.',
            ],
            [
                'q' => 'What areas does SmooveHub deliver to?',
                'a' => 'SmooveHub and CRS currently serve all major US cities. Orders within 0–50 miles are auto-approved. 51–100 miles require CRM confirmation. 101–200 miles require route validation. 200+ miles require a custom quote — call 877-385-2919.',
            ],
            [
                'q' => 'Is my payment information secure?',
                'a' => 'Yes. All payments are processed by Stripe (PCI-DSS Level 1 compliant). Your card details are never stored on SmooveHub servers. Seller banking info is AES-256 encrypted and only accessible to authorized payout personnel.',
            ],
            [
                'q' => 'How do I list a product as a seller?',
                'a' => 'Log into your Seller Dashboard, click "Add Listing," and fill in the product name, description, price, condition, and photos. Our AI automatically generates SEO metadata to improve your listing\'s visibility. Listings go live immediately after publishing.',
            ],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function quoteSurcharges(): array
    {
        return [
            ['item' => 'CRM base delivery', 'amount' => 'By route'],
            ['item' => 'In-home delivery', 'amount' => 'Included — FREE', 'tone' => 'green'],
            ['item' => 'Stairs (per flight)', 'amount' => '+$25'],
            ['item' => 'Spiral staircase', 'amount' => '+$50'],
            ['item' => 'Narrow door (<32")', 'amount' => '+$40'],
            ['item' => 'No elevator + stairs', 'amount' => '+$30'],
            ['item' => 'SmooveHub fee', 'amount' => '+3% of CRM base'],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function quoteDistanceRules(): array
    {
        return [
            ['icon' => '📗', 'range' => '0–50 mi', 'status' => 'Auto-approved'],
            ['icon' => '📙', 'range' => '51–100 mi', 'status' => 'CRM confirmation'],
            ['icon' => '📕', 'range' => '101–200 mi', 'status' => 'Validation required'],
            ['icon' => '📞', 'range' => '200+ mi', 'status' => 'Custom quote'],
        ];
    }
}
