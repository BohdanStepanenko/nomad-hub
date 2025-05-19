<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory;

    public const ADMIN = 'Admin';

    public const CLIENT = 'Client';

    public static function allRoles(): Collection
    {
        return collect(
            [
                self::ADMIN,
                self::CLIENT,
            ]
        );
    }
}
