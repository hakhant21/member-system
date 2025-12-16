<?php

namespace DET\Members\Console\Commands;

use Illuminate\Console\Command;

class InstallMemberSystem extends Command
{
    protected $signature = 'member-system:install';

    protected $description = 'Install the Member System Package';

    public function handle()
    {
        $this->info('Installing Member System...');

        // 1. Check Dependencies
        $dependencies = ['laravel/sanctum', 'spatie/laravel-permission'];
        $missing = [];

        foreach ($dependencies as $dep) {
            if (! class_exists(\Laravel\Sanctum\Sanctum::class) && $dep === 'laravel/sanctum') {
                $missing[] = $dep;
            }
            if (! class_exists(\Spatie\Permission\PermissionServiceProvider::class) && $dep === 'spatie/laravel-permission') {
                $missing[] = $dep;
            }
        }

        if (! empty($missing)) {
            $this->error('Missing required packages: '.implode(', ', $missing));
            $this->warn('Please install them via composer require first.');

            return;
        }

        // 2. Publish Config
        $this->call('vendor:publish', [
            '--tag' => 'member-system-config',
        ]);

        // 3. Ask to Migrate
        if ($this->confirm('Do you want to run the migrations now?', true)) {
            $this->call('migrate');
            $this->info('Migrations ran successfully.');
        }

        $this->info('Member System Installed Successfully! 🚀');
    }
}
