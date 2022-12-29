<?php
declare(strict_types=1);

namespace Kosher\WineStore\Service;

use Magento\Framework\App\ResourceConnection;

class SetRootCategoryWineStoreService
{
    private const TARGET_TABLE_NAME = 'catalog_category_entity_varchar';
    private const STORE_NAME = 'Ariskosherwine';

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @return int
     */
    public function execute(): int
    {
        $rootCategoryId = (int)$this->getRootCategoryId();
        if (!empty($rootCategoryId)) {
            $this->updateRootCategoryWine($rootCategoryId);
        }

        return $rootCategoryId;
    }

    /**
     * @return string
     */
    private function getRootCategoryId(): string
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select();

        $select->from(self::TARGET_TABLE_NAME, 'entity_id')->where('value' . ' = ?', self::STORE_NAME);

        return $connection->fetchOne($select);
    }

    /**
     * @param int $rootCategoryId
     * @return void
     */
    private function updateRootCategoryWine(int $rootCategoryId): void
    {
        $this->resourceConnection->getConnection()
            ->update(
                $this->resourceConnection->getTableName('store_group'),
                ['root_category_id' => $rootCategoryId],
                ['name = ?' => 'Ariskosherwine Store']
            );
    }
}
