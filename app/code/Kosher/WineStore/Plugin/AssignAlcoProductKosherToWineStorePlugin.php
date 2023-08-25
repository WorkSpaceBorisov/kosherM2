<?php
declare(strict_types=1);

namespace Kosher\WineStore\Plugin;

use Kosher\WineStore\Service\Category\SetAnchorToCategoriesService;
use Kosher\WineStore\Service\ImportProduct\AssignAlcoProductKosherToWineStoreService;
use Magento\ImportExport\Model\Import;

class AssignAlcoProductKosherToWineStorePlugin
{
    /**
     * @var AssignAlcoProductKosherToWineStoreService
     */
    private AssignAlcoProductKosherToWineStoreService $alcoProductKosherToWineStoreService;

    /**
     * @var SetAnchorToCategoriesService
     */
    private SetAnchorToCategoriesService $setAnchorToCategoriesService;

    /**
     * @param AssignAlcoProductKosherToWineStoreService $alcoProductKosherToWineStoreService
     * @param SetAnchorToCategoriesService $setAnchorToCategoriesService
     */
    public function __construct(
        AssignAlcoProductKosherToWineStoreService $alcoProductKosherToWineStoreService,
        SetAnchorToCategoriesService $setAnchorToCategoriesService
    ) {
        $this->alcoProductKosherToWineStoreService = $alcoProductKosherToWineStoreService;
        $this->setAnchorToCategoriesService = $setAnchorToCategoriesService;
    }

    /**
     * @param Import $subject
     * @param bool $result
     * @return bool
     * @throws \Exception
     */
    public function afterImportSource(Import $subject, bool $result): bool
    {
        $this->alcoProductKosherToWineStoreService->execute();
        $this->setAnchorToCategoriesService->execute();

        return $result;
    }
}
