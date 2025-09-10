<?php

namespace App\Enum;

enum RoleEnum: string
{
    case Superadmin = 'Superadministrator';
    case Administrator = 'Administrator';
    case User = 'User';
}
