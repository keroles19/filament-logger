<?php

namespace Keroles\FilamentLogger\Authorization;

use Illuminate\Contracts\Auth\Authenticatable;
use Keroles\FilamentLogger\Contracts\AuthorizesActivityDeletion;

/**
 * Default: activity rows cannot be deleted until the host app binds
 * {@see AuthorizesActivityDeletion} to a custom implementation or uses policies.
 */
class DenyAllActivityDeletion implements AuthorizesActivityDeletion
{
    public function canDelete(?Authenticatable $user): bool
    {
        return false;
    }
}
