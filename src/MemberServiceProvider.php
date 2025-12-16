<?php

namespace DET\Members;

use DET\Members\Console\Commands\InstallMemberSystem;
use DET\Members\Services\Contracts\MemberServiceInterface;
use DET\Members\Services\MemberService;
use Illuminate\Support\ServiceProvider;

class MemberServiceProvider extends ServiceProvider
{
    public function register()
    {
        // 1. Merge Config
        $this->mergeConfigFrom(
            __DIR__.'/config/members.php', 'member-system'
        );

        // 2. Bind Interface to Implementation (Dependency Injection)
        $this->app->bind(MemberServiceInterface::class, MemberService::class);
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');

        // 3. Register Command
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallMemberSystem::class,
            ]);

            // 4. Publish Config
            $this->publishes([
                __DIR__.'/config/members.php' => config_path('member-system.php'),
            ], 'member-system-config');
        }
    }
}
