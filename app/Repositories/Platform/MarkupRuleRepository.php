<?php

namespace App\Repositories\Platform;

use App\Models\Platform\MarkupRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class MarkupRuleRepository
{
    /**
     * @return Collection<string, float>
     */
    public function activeRates(): Collection
    {
        if (! Schema::hasTable('markup_rules')) {
            return collect();
        }

        return MarkupRule::query()
            ->where('is_active', true)
            ->get()
            ->pluck('rate', 'rule_key')
            ->map(fn ($rate) => (float) $rate);
    }
}
