<?php
declare(strict_types=1);

namespace Kosher\ProductAdjustment\Service\Attribute;

class CheckCurrentAndLastDateService
{
    /**
     * @param string $dateTimeInput
     * @return bool
     */
    public function execute(string $dateTimeInput): bool
    {
        $timestampInput = strtotime($dateTimeInput);
        $currentTime = time();
        return !($currentTime > $timestampInput);
    }
}
