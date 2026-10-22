<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Demo\DemoSandbox;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('demo:prune')]
#[Description('Delete demo sandboxes past their lifetime, with their shelter data and files')]
class PruneDemoSandboxes extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(DemoSandbox $sandbox): int
    {
        $this->info('Deleted '.$sandbox->prune().' demo sandbox(es).');

        return self::SUCCESS;
    }
}
