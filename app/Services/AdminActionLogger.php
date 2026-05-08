<?php

namespace App\Services;

use App\Models\AdminActionLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AdminActionLogger
{
    /**
     * @param  array<string, mixed>  $meta
     */
    public function log(User $admin, string $action, Model|string $target, ?int $targetId = null, array $meta = []): void
    {
        AdminActionLog::create([
            'admin_user_id' => $admin->id,
            'action' => $action,
            'target_type' => $target instanceof Model ? $target::class : $target,
            'target_id' => $target instanceof Model ? $target->getKey() : $targetId,
            'meta' => $meta,
        ]);
    }
}
