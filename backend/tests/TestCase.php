<?php

namespace Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication(): Application
    {
        $this->useSqliteInMemoryForTests();

        return parent::createApplication();
    }

    private function useSqliteInMemoryForTests(): void
    {
        $set = [
            'DB_CONNECTION' => 'sqlite',
            'DB_DATABASE' => ':memory:',
        ];
        foreach ($set as $key => $value) {
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }

        foreach (['DB_HOST', 'DB_PORT', 'DB_USERNAME', 'DB_PASSWORD', 'DB_URL'] as $key) {
            putenv($key);
            unset($_ENV[$key], $_SERVER[$key]);
        }
    }
}
