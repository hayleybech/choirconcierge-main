<?php

namespace Tests;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected bool $tenancy = true;

    public function setUp(): void
    {
        parent::setUp();

        if ($this->tenancy) {
            $this->initializeTenancy();
        }
    }

    public function initializeTenancy(): void
    {
        $tenant = Tenant::create(['id' => 'test']);
        $tenant->domains()->create(['domain' => 'test.choirconcierge.com']);
        tenancy()->initialize($tenant);
    }
}
