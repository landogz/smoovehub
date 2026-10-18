<?php

namespace App\Services\Audit;

use App\Models\Platform\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogger
{
    /**
     * @param  array<string, mixed>  $properties
     */
    public function log(?User $user, string $action, ?string $subjectType, ?int $subjectId, array $properties = [], ?Request $request = null): void
    {
        ActivityLog::query()->create([
            'user_id' => $user?->id,
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'properties' => $properties,
            'ip_address' => $request?->ip(),
            'user_agent' => $request ? substr((string) $request->userAgent(), 0, 500) : null,
        ]);
    }
}
