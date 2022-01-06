<?php

namespace App\Build\Commands;

use App\Jobs\ProcessExecutableGetCompiledAt;
use App\Jobs\ProcessRoot;
use App\Models\Build;
use Illuminate\Console\Command;
use Illuminate\Support\LazyCollection;

class BuildProcess extends Command
{
    /** @inerhitDoc */
    protected $signature = 'build:process {buildId=all}';

    /** @inerhitDoc */
    protected $description = 'Process a build by Id';

    /** @inerhitDoc */
    public function __construct()
    {
        parent::__construct();
    }

    /** @inerhitDoc */
    public function handle(): int
    {
        if (PHP_OS_FAMILY !== 'Linux') {
            $this->error('This command only works under linux!');
            return Command::FAILURE;
        }

        $buildId = $this->argument('buildId');

        /** @var Build[]|LazyCollection $builds */
        if ($buildId !== 'all') {
            $build = Build::find($this->argument('buildId'));
            if (!$build) {
                $this->error('Build not found');
                return Command::FAILURE;
            }

            $builds = [$build];
        } else {
            $builds = Build::orderBy('clientBuild')->cursor();
        }

        /** @var Build $build */
        foreach ($builds as $build) {
            $this->info(sprintf('Process executable to get compiled at form build %s.%u', $build->patch, $build->clientBuild));
            ProcessExecutableGetCompiledAt::dispatchSync($build, true);

            $this->info(sprintf('Process root for build %s.%u', $build->patch, $build->clientBuild));
            ProcessRoot::dispatchSync($build, true);

            echo "\n";
        }

        return Command::SUCCESS;
    }
}
