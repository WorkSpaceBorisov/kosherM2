<?php
declare(strict_types=1);

namespace Kosher\WineStore\Service\ImportProduct;

use Magento\Framework\App\ResourceConnection;

class SetAnchorFotWineCategoriesService
{
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
     * @param int $rootCategoryId
     * @return void
     */
    public function execute(int $rootCategoryId): void
    {
        $attributeId = $this->getAnchorAttributeId();
        $this->setAttributeValueIsAnchor($attributeId, $rootCategoryId);
    }

    /**
     * @return int
     */
    private function getAnchorAttributeId(): int
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select();

        $select->from('eav_attribute', 'attribute_id')->where('attribute_code' . ' = ?', 'is_anchor');

        return (int)$connection->fetchOne($select);
    }

    /**
     * @param int $attributeId
     * @param int $rootCategoryId
     * @return void
     */
    private function setAttributeValueIsAnchor(int $attributeId, int $rootCategoryId): void
    {
        $this->resourceConnection->getConnection()
            ->update(
                $this->resourceConnection->getTableName('catalog_category_entity_int'),
                ['value' => 0],
                [
                    'entity_id > ?' => $rootCategoryId,
                    'attribute_id = ?' => $attributeId
                ]
            );
    }
}
