<?php
declare(strict_types=1);

namespace Kosher\WineStore\Plugin;

use Kosher\WineStore\Service\ImportProduct\SetAnchorFotWineCategoriesService;
use Kosher\WineStore\Service\ImportProduct\SetRootCategoryWineStoreService;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;

class SetRootCategoryWineStorePlugin
{
    /**
     * @var SetAnchorFotWineCategoriesService
     */
    private SetAnchorFotWineCategoriesService $setAnchorFotWineCategoriesService;

    /**
     * @param SetRootCategoryWineStoreService $setRootCategoryWineStoreService
     * @param SetAnchorFotWineCategoriesService $setAnchorFotWineCategoriesService
     */
    public function __construct(
        SetRootCategoryWineStoreService $setRootCategoryWineStoreService,
        SetAnchorFotWineCategoriesService $setAnchorFotWineCategoriesService
    ) {
        $this->setRootCategoryWineStoreService = $setRootCategoryWineStoreService;
        $this->setAnchorFotWineCategoriesService = $setAnchorFotWineCategoriesService;
    }
    /**
     * @param AbstractEntity $subject
     * @param bool $result
     * @return bool
     */
    public function afterImportData(AbstractEntity $subject, bool $result): bool
    {
        $behavior = $subject->getBehavior();
        if ($behavior != 'delete' && $behavior != 'replace') {
            $rootCategoryId = $this->setRootCategoryWineStoreService->execute();
            $this->setAnchorFotWineCategoriesService->execute($rootCategoryId);
        }
        return $result;
    }
}
