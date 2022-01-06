<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Build\Helper\BuildProcessor;
use App\Common\Services\TactService;
use App\Models\Build;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessExecutableGetCompiledAt implements ShouldQueue
{
    use BuildProcessor, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const QUERY_BUFFER_SIZE = 7000;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 1800;

    public function __construct(private Build $build, private $force = false)
    {
        //
    }

    public function handle(TactService $tactService): void
    {
        if (!$this->force && $this->alreadyProcessed()) {
            return;
        }

        ini_set('memory_limit', '256M');
        $install = $tactService->getInstallByBuild($this->build);

        $executableNames = [
            // Mainline
            'Wow.exe', 'WowT.exe', 'WowB.exe',
            // Classic
            'WowClassic.exe', 'WowClassicT.exe', 'WowClassicB.exe',
            // Old 64-bit specific builds
            'Wow-64.exe', 'WowT-64.exe', 'WowB-64.exe',
        ];

        /** @var ?string $executableContentHash */
        $executableContentHash = null;
        foreach ($executableNames as $executableName) {
            $contentHash = $install->getContentHash($executableName);
            if ($contentHash !== null) {
                $executableContentHash = bin2hex($contentHash);
                break;
            }
        }

        if ($executableContentHash === null) {
            $this->markAsProcessed();
            return;
        }

        $executableTempPath = tempnam(sys_get_temp_dir(), $executableName);
        if (!$tactService->downloadFileByContentHashWithBuild($executableContentHash, $executableTempPath, $this->build) || !file_exists($executableTempPath)) {
            $this->unlinkAndMarkAsProcessed($executableTempPath);
            return;
        }

        $executableContent = file_get_contents($executableTempPath);

        $timeMatch = [];
        preg_match('/Exe\s+Built:\s+(\w{3}\s+\d{1,2}\s+\d{4}\s+\d{2}:\d{2}:\d{2})/i', $executableContent, $timeMatch);

        if (empty($timeMatch[1])) {
            $this->unlinkAndMarkAsProcessed($executableTempPath);
            return;
        }

        $this->build->compiledAt = Carbon::createFromTimeString(preg_replace('/\s{2,}/i', ' ', $timeMatch[1]));
        $this->build->update();

        $this->unlinkAndMarkAsProcessed($executableTempPath);
    }

    private function unlinkAndMarkAsProcessed(string $executableTempPath)
    {
        if (file_exists($executableTempPath)) {
            unlink($executableTempPath);
        }

        $this->markAsProcessed();
    }
}
