<?php

namespace Keroles\FilamentLogger\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface AuthorizesActivityDeletion
{
    /**
     * Whether the given user may delete activity log records (single or bulk).
     */
    public function canDelete(?Authenticatable $user): bool;
}
