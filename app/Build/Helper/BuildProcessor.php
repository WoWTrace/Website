<?php
declare(strict_types=1);


namespace App\Build\Helper;

trait BuildProcessor
{

    public function alreadyProcessed(): bool
    {
        return in_array(static::class, $this->build->processedBy, true);
    }

    public function markAsProcessed(): void
    {
        $processedBuilds = $this->build->processedBy;
        $processedBuilds[] = static::class;
        $this->build->processedBy = array_unique($processedBuilds);
        $this->build->update();
    }
}