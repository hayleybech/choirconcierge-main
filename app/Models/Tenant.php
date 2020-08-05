<?php


namespace App\Models;

use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant As BaseTenant;

class Tenant extends BaseTenant
{
    use HasDomains;
}