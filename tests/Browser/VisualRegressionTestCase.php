<?php

namespace Tests\Browser;

use Illuminate\Foundation\Application;
use RuntimeException;

class VisualRegressionTestCase extends \Tests\TestCase
{
    protected bool $tenancy = false;
    /** @var array{environment: string|false, server: string|null, env: string|null} */
    private array $originalCentralDomain = [];

    /** @var array{environment: string|false, server: string|null, env: string|null} */
    private array $originalSessionDriver = [];

    public function refreshDatabase(): void
    {
        if (getenv('VISUAL_REGRESSION_DATABASE_PREPARED') === '1') {
            return;
        }

        parent::refreshDatabase();
    }

    public function createApplication(): Application
    {
        $this->setCentralDomainForBrowserServer();

        $app = parent::createApplication();

        $database = $app['config']->get('database.connections.mysql.database');

        if (! $app->environment('testing')
            || $app['config']->get('database.default') !== 'mysql'
            || ! is_string($database)
            || trim($database) === '') {
            throw new RuntimeException('Visual browser tests require APP_ENV=testing and a configured MySQL test database.');
        }

        return $app;
    }

    protected function tearDown(): void
    {
        try {
            parent::tearDown();
        } finally {
            $this->restoreCentralDomain();
            $this->restoreSessionDriver();
        }
    }

    private function setCentralDomainForBrowserServer(): void
    {
        $this->originalCentralDomain = [
            'environment' => getenv('TENANCY_CENTRAL_DOMAIN'),
            'server' => $_SERVER['TENANCY_CENTRAL_DOMAIN'] ?? null,
            'env' => $_ENV['TENANCY_CENTRAL_DOMAIN'] ?? null,
        ];

        putenv('TENANCY_CENTRAL_DOMAIN=127.0.0.1');
        $_SERVER['TENANCY_CENTRAL_DOMAIN'] = '127.0.0.1';
        $_ENV['TENANCY_CENTRAL_DOMAIN'] = '127.0.0.1';

        $this->originalSessionDriver = [
            'environment' => getenv('SESSION_DRIVER'),
            'server' => $_SERVER['SESSION_DRIVER'] ?? null,
            'env' => $_ENV['SESSION_DRIVER'] ?? null,
        ];

        putenv('SESSION_DRIVER=file');
        $_SERVER['SESSION_DRIVER'] = 'file';
        $_ENV['SESSION_DRIVER'] = 'file';
    }

    private function restoreCentralDomain(): void
    {
        $environment = $this->originalCentralDomain['environment'];
        if ($environment === false) {
            putenv('TENANCY_CENTRAL_DOMAIN');
        } else {
            putenv('TENANCY_CENTRAL_DOMAIN='.$environment);
        }

        foreach (['server' => $_SERVER, 'env' => $_ENV] as $key => $values) {
            $originalValue = $this->originalCentralDomain[$key];
            $variable = 'TENANCY_CENTRAL_DOMAIN';

            if ($originalValue === null) {
                unset($values[$variable]);
            } else {
                $values[$variable] = $originalValue;
            }

            if ($key === 'server') {
                $_SERVER = $values;
            } else {
                $_ENV = $values;
            }
        }
    }

    private function restoreSessionDriver(): void
    {
        $environment = $this->originalSessionDriver['environment'];
        if ($environment === false) {
            putenv('SESSION_DRIVER');
        } else {
            putenv('SESSION_DRIVER='.$environment);
        }

        foreach (['server' => $_SERVER, 'env' => $_ENV] as $key => $values) {
            $originalValue = $this->originalSessionDriver[$key];

            if ($originalValue === null) {
                unset($values['SESSION_DRIVER']);
            } else {
                $values['SESSION_DRIVER'] = $originalValue;
            }

            if ($key === 'server') {
                $_SERVER = $values;
            } else {
                $_ENV = $values;
            }
        }
    }
}