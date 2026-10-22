<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Shelter;
use App\Models\User;
use Database\Seeders\DocumentationDemoSeeder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

#[Signature('demo:prepare')]
#[Description('Seed the demo template shelter on an empty database and lock the seeded example accounts')]
class PrepareDemo extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! config('demo.enabled')) {
            $this->error('DEMO_MODE is not enabled.');

            return self::FAILURE;
        }

        if (Shelter::query()->withTrashed()->exists()) {
            $this->info('The template shelter is already seeded.');

            return self::SUCCESS;
        }

        $this->call('db:seed', ['--class' => DocumentationDemoSeeder::class, '--force' => true]);

        // The seeder's example logins have a well-known password; nobody may use them on a public demo.
        User::query()->each(fn (User $user) => $user->forceFill(['password' => Str::random(40)])->save());

        return self::SUCCESS;
    }
}
