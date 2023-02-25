<?php
declare(strict_types=1);

namespace Kosher\ProductAttributeSetExportImport\Service\AttributeSet;

use Magento\Framework\App\ResourceConnection;

class SaveEavEntityAttributeDataService
{
    private const DESTINATION_TABLE = 'eav_entity_attribute';

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
     * @param int $attributeSetId
     * @param int $attributeGroupId
     * @param int $attributeId
     * @return void
     */
    public function execute(int $attributeSetId, int $attributeGroupId, int $attributeId): void
    {
        $this->resourceConnection->getConnection()->insert(
            $this->resourceConnection->getConnection()->getTableName(self::DESTINATION_TABLE),
            [
                'entity_type_id' => 4,
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'attribute_id' => $attributeId,
                'sort_order' => 0,
            ],
        );
    }
}
