<?php
declare(strict_types=1);

namespace Kosher\ProductAttributeSetExportImport\Service\AttributeSet;

use Magento\Framework\App\ResourceConnection;

class GetAttributeSetIdService
{
    private const TARGET_TABLE_NAME = 'eav_attribute_set';
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
     * @param string $attributeSetName
     * @return int
     */
    public function execute(string $attributeSetName): int
    {
        return $this->getAttributeSetIdByNameQuery($attributeSetName);
    }

    /**
     * @param string $attributeSetName
     * @return int
     */
    private function getAttributeSetIdByNameQuery(string $attributeSetName): int
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select();

        $select->from(self::TARGET_TABLE_NAME, 'attribute_set_id')
            ->where('entity_type_id' . ' = ?', 4)
            ->where('attribute_set_name' . ' = ?', $attributeSetName);

        return (int)$connection->fetchOne($select);
    }
}
