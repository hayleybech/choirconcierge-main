<?php

namespace Tests;

use JMac\Testing\Traits\AdditionalAssertions;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, AdditionalAssertions;

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
        $tenant = Tenant::create([
            'id' => 'test',
            'choir_name' => 'Hypothetical Harmony',
        ]);
        $tenant->domains()->create(['domain' => 'test']);
        $tenant->save();

        tenancy()->initialize($tenant);
    }
}
