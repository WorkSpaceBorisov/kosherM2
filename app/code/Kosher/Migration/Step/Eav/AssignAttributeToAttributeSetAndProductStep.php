<?php

namespace Kosher\Migration\Step\Eav;

use Magento\Eav\Api\AttributeManagementInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Migration\App\Step\StageInterface;
use Migration\ResourceModel\Destination;
use Migration\ResourceModel\Source;

class AssignAttributeToAttributeSetAndProductStep implements StageInterface
{
    private const PRODUCT_DETAILS_GROUP_NAME = 'Product Details';
    private const ATTRIBUTE_CODE = 'manufacturer';
    private const ATTRIBUTESET_NAME_KOSHER4U = 'Kosher4u';

    private const SOURCE_EAV_ATTRIBUTE_TABLE = 'eav_attribute';
    private const SOURCE_EAV_ATTRIBUTE_FIELD_ATTRIBUTE_ID = 'attribute_id';
    private const SOURCE_EAV_ATTRIBUTE_FIELD_ATTRIBUTE_CODE = 'attribute_code';
    private const SOURCE_CATALOG_PRODUCT_ENTITY_INT_TABLE = 'catalog_product_entity_int';
    private const SOURCE_CATALOG_PRODUCT_ENTITY_FIELD_ENTITY_ID = 'entity_id';
    private const SOURCE_CATALOG_PRODUCT_ENTITY_FIELD_ATTRIBUTE_ID = 'attribute_id';
    private const SOURCE_CATALOG_PRODUCT_ENTITY_FIELD_STORE_ID = 'store_id';
    private const SOURCE_CATALOG_PRODUCT_ENTITY_FIELD_VALUE = 'value';

    private const DESTINATION_ATTRIBUTE_SET_TABLE = 'eav_attribute_set';
    private const DESTINATION_ATTRIBUTE_SET_TABLE_FIELD_ID = 'attribute_set_id';
    private const DESTINATION_ATTRIBUTE_SET_TABLE_FIELD_NAME = 'attribute_set_name';

    private const DESTINATION_ATTRIBUTE_GROUP_TABLE = 'eav_attribute_group';
    private const DESTINATION_ATTRIBUTE_GROUP_ID = 'attribute_group_id';
    private const DESTINATION_ATTRIBUTE_GROUP_NAME = 'attribute_group_name';
    private const DESTINATION_CATALOG_PRODUCT_ENTITY_INT_TABLE = 'catalog_product_entity_int';

    /**
     * @var Destination
     */
    private Destination $destination;

    /**
     * @var Source
     */
    private Source $source;

    /**
     * @var AttributeManagementInterface
     */
    private AttributeManagementInterface $attributeManagement;

    /**
     * @param Destination $destination
     * @param Source $source
     * @param AttributeManagementInterface $attributeManagement
     */
    public function __construct(
        Destination $destination,
        Source $source,
        AttributeManagementInterface $attributeManagement
    ) {
        $this->destination = $destination;
        $this->source = $source;
        $this->attributeManagement = $attributeManagement;
    }

    /**
     * @return bool
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function perform(): bool
    {
        $attributeSetId = $this->getDestinationAttributeSetId();
        $group_id = $this->getDestinationAttributeGroupIdByName();
        $this->attributeManagement->assign(
            'catalog_product',
            $attributeSetId,
            $group_id,
            self::ATTRIBUTE_CODE,
            3
        );

        $attributeId = $this->getSourceAttributeId();
        $productsWithAttributeValue = $this->getProductsWithOptionsByAttributeId($attributeId);
        foreach ($productsWithAttributeValue as $data) {
            $this->saveDataToDestinationTable(self::DESTINATION_CATALOG_PRODUCT_ENTITY_INT_TABLE, $data);
        }

        return true;
    }

    /**
     * @return int
     */
    private function getDestinationAttributeSetId(): int
    {
        $query = $this->destination->getAdapter()->getSelect()
            ->from(
                $this->source->addDocumentPrefix(self::DESTINATION_ATTRIBUTE_SET_TABLE),
                [
                    self::DESTINATION_ATTRIBUTE_SET_TABLE_FIELD_ID,
                ]
            )->where(self::DESTINATION_ATTRIBUTE_SET_TABLE_FIELD_NAME . '= ?', self::ATTRIBUTESET_NAME_KOSHER4U);

        return (int)$query->getAdapter()->fetchOne($query);
    }

    /**
     * @return int
     */
    private function getDestinationAttributeGroupIdByName(): int
    {
        $query = $this->destination->getAdapter()->getSelect()
            ->from(
                $this->source->addDocumentPrefix(self::DESTINATION_ATTRIBUTE_GROUP_TABLE),
                [
                    self::DESTINATION_ATTRIBUTE_GROUP_ID,
                ]
            )->where(self::DESTINATION_ATTRIBUTE_GROUP_NAME . '= ?', self::PRODUCT_DETAILS_GROUP_NAME);

        return (int)$query->getAdapter()->fetchOne($query);
    }

    /**
     * @return int
     */
    private function getSourceAttributeId(): int
    {
        $query = $this->source->getAdapter()->getSelect()
            ->from(
                $this->source->addDocumentPrefix(self::SOURCE_EAV_ATTRIBUTE_TABLE),
                [
                    self::SOURCE_EAV_ATTRIBUTE_FIELD_ATTRIBUTE_ID,
                ]
            )->where(self::SOURCE_EAV_ATTRIBUTE_FIELD_ATTRIBUTE_CODE . '= ?', self::ATTRIBUTE_CODE);

        return (int)$query->getAdapter()->fetchOne($query);
    }

    /**
     * @param int $attributeId
     * @return array
     */
    private function getProductsWithOptionsByAttributeId(int $attributeId): array
    {
        $query = $this->source->getAdapter()->getSelect()
            ->from(
                $this->source->addDocumentPrefix(self::SOURCE_CATALOG_PRODUCT_ENTITY_INT_TABLE),
                [
                    self::SOURCE_CATALOG_PRODUCT_ENTITY_FIELD_ATTRIBUTE_ID,
                    self::SOURCE_CATALOG_PRODUCT_ENTITY_FIELD_STORE_ID,
                    self::SOURCE_CATALOG_PRODUCT_ENTITY_FIELD_ENTITY_ID,
                    self::SOURCE_CATALOG_PRODUCT_ENTITY_FIELD_VALUE,
                ]
            )->where(self::SOURCE_CATALOG_PRODUCT_ENTITY_FIELD_ATTRIBUTE_ID . '= ?', $attributeId);

        return $query->getAdapter()->fetchAll($query);
    }

    /**
     * @param string $table
     * @param array $data
     * @return void
     */
    private function saveDataToDestinationTable(string $table, array $data): void
    {
        $this->destination->getAdapter()->updateChangedRecords(
            $this->destination->addDocumentPrefix($table),
            $data
        );
    }
}
