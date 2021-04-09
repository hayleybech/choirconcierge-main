<?php

namespace Tests;

use JMac\Testing\Traits\AdditionalAssertions;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, AdditionalAssertions;

    protected bool $tenancy = true;

    protected function setUp(): void
    {
        parent::setUp();

        if ($this->tenancy) {
            $this->initializeTenancy();
        }
    }

    public function initializeTenancy(): void
    {
        Tenant::find('phpunit')?->delete();
    	$tenant = Tenant::create(
            id: 'phpunit',
            choir_name: 'PHPUnit Testing',
	        timezone: 'Australia/Perth',
        );
        $tenant->domains()->create(['domain' => 'phpunit']);
        $tenant->save();

        tenancy()->initialize($tenant);
    }
}
