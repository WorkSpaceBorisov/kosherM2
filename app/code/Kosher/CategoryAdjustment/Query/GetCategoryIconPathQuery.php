<?php
declare(strict_types=1);

namespace Kosher\CategoryAdjustment\Query;

use Magento\Framework\App\ResourceConnection;

class GetCategoryIconPathQuery
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
     * @param int $attributeId
     * @param int $categoryId
     * @return string|null
     */
    public function execute(int $attributeId, int $categoryId): ?string
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select();

        $select->from('catalog_category_entity_varchar', 'value')
            ->where('attribute_id = ?', $attributeId)
            ->where('entity_id = ?', $categoryId);

        $result = $connection->fetchOne($select);

        if (empty($result) || $result == null){
            return null;
        }
        return $result;
    }
}
