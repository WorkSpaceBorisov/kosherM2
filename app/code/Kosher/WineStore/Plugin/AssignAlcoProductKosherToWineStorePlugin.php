<?php
declare(strict_types=1);

namespace Kosher\WineStore\Plugin;

use Kosher\WineStore\Service\ImportProduct\AssignAlcoProductKosherToWineStoreService;
use  Magento\ImportExport\Model\Import;

class AssignAlcoProductKosherToWineStorePlugin
{
    /**
     * @var AssignAlcoProductKosherToWineStoreService
     */
    private AssignAlcoProductKosherToWineStoreService $alcoProductKosherToWineStoreService;

    /**
     * @param AssignAlcoProductKosherToWineStoreService $alcoProductKosherToWineStoreService
     */
    public function __construct(
        AssignAlcoProductKosherToWineStoreService $alcoProductKosherToWineStoreService
    ) {
        $this->alcoProductKosherToWineStoreService = $alcoProductKosherToWineStoreService;
    }

    /**
     * @param Import $subject
     * @param bool $result
     * @return bool
     */
    public function afterImportSource(Import $subject, bool $result): bool
    {
        $this->alcoProductKosherToWineStoreService->execute();
        return $result;
    }
}
