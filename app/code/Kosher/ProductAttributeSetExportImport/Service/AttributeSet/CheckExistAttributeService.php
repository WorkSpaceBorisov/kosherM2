<?php
declare(strict_types=1);

namespace Kosher\ProductAttributeSetExportImport\Service\AttributeSet;

use Magento\Framework\App\ResourceConnection;

class CheckExistAttributeService
{
    private const TARGET_TABLE_NAME = 'eav_attribute';

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
     * @param string $attributeCode
     * @return bool|int
     */
    public function execute(string $attributeCode): bool|int
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select();

        $select->from(self::TARGET_TABLE_NAME, 'attribute_id')
            ->where('entity_type_id' . ' = ?', 4)
            ->where('attribute_code' . ' = ?', $attributeCode);

        $result = $connection->fetchOne($select);

        return !empty($result) ? (int)$result : false;
    }
}
