<?php
declare(strict_types=1);

namespace Kosher\ProductAttributeSetExportImport\Service\AttributeSet;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\ResourceConnection;

class SaveCatalogEavAttributeService
{
    private const DESTINATION_TABLE = 'catalog_eav_attribute';

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @var AttributeFactory
     */
    private AttributeFactory $attributeFactory;

    /**
     * @var DataObjectHelper
     */
    private DataObjectHelper $dataObjectHelper;

    /**
     * @param AttributeFactory $attributeFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        AttributeFactory $attributeFactory,
        DataObjectHelper $dataObjectHelper,
        ResourceConnection $resourceConnection
    ) {
        $this->attributeFactory = $attributeFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param array $attributeData
     * @return void
     */
    public function execute(array $attributeData): void
    {
        /** @var Attribute $eavAttribute */
        $eavAttribute = $this->attributeFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $eavAttribute,
            $attributeData,
            Attribute::class
        );

        $this->saveCatalogEavAttribute($eavAttribute->getData());
    }

    /**
     * @param array $data
     * @return void
     */
    private function saveCatalogEavAttribute(array $data): void
    {
        $this->resourceConnection->getConnection()->insert(
            $this->resourceConnection->getConnection()->getTableName(self::DESTINATION_TABLE),
            $data,
        );
    }
}
