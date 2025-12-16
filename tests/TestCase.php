<?php

namespace DET\Members\Tests;

use DET\Members\MemberServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\SanctumServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Permission\PermissionServiceProvider;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    public $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Run Spatie Migrations (Keep this, it works)
        $migration = include __DIR__.'/../vendor/spatie/laravel-permission/database/migrations/create_permission_tables.php.stub';
        $migration->up();

        // 2. 🟢 FIX: Load Sanctum Migrations using absolute path
        $this->loadMigrationsFrom(__DIR__.'/../vendor/laravel/sanctum/database/migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            PermissionServiceProvider::class,
            SanctumServiceProvider::class,
            MemberServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'MemberSystem' => \DET\Members\Facades\MemberSystem::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // 1. Database
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // 2. Auth Guards & Providers
        $app['config']->set('auth.guards.member', [
            'driver' => 'session',
            'provider' => 'members',
        ]);

        $app['config']->set('auth.providers.members', [
            'driver' => 'eloquent',
            'model' => \DET\Members\Models\Member::class,
        ]);

        // 3. Sanctum Config
        $app['config']->set('auth.guards.sanctum', [
            'driver' => 'sanctum',
            'provider' => null,
        ]);

        // 🟢 4. Hashing Config (FIXES YOUR ERROR)
        $app['config']->set('hashing.driver', 'bcrypt');
        $app['config']->set('hashing.bcrypt', [
            'rounds' => 4, // Low rounds makes tests faster
            'verify' => true,
        ]);
    }
}
