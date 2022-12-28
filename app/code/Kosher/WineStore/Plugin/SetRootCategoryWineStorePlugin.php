<?php
declare(strict_types=1);

namespace Kosher\WineStore\Plugin;

use Kosher\WineStore\Service\SetRootCategoryWineStoreService;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;

class SetRootCategoryWineStorePlugin
{
    /**
     * @var SetRootCategoryWineStoreService
     */
    private SetRootCategoryWineStoreService $setRootCategoryWineStoreService;

    /**
     * @param SetRootCategoryWineStoreService $setRootCategoryWineStoreService
     */
    public function __construct(
        SetRootCategoryWineStoreService $setRootCategoryWineStoreService
    ) {
        $this->setRootCategoryWineStoreService = $setRootCategoryWineStoreService;
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
            $this->setRootCategoryWineStoreService->execute();
        }
        return $result;
    }
}
