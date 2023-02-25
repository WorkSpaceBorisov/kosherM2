<?php
declare(strict_types=1);

namespace Kosher\ProductAttributeSetExportImport\Service\AttributeSet;

use Magento\Framework\App\ResourceConnection;

class CheckExistGroupByAttributeSetIdService
{
    private const TARGET_TABLE_NAME = 'eav_attribute_group';

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
    public function execute(string $attributeGroupName, int $attributeSetId): bool|int
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select();

        $select->from(self::TARGET_TABLE_NAME, 'attribute_group_id')
            ->where('attribute_set_id' . ' = ?', $attributeSetId)
            ->where('attribute_group_name' . ' = ?', $attributeGroupName);

        $result = $connection->fetchOne($select);

        return !empty($result) ? (int)$result : false;
    }
}
