<?php
declare(strict_types=1);

namespace Kosher\ProductAttributeSetExportImport\Service\AttributeSet;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Eav\Api\AttributeGroupRepositoryInterface;
use Magento\Eav\Api\Data\AttributeGroupInterfaceFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Serialize\Serializer\Json;

class AttributeSetSaveProcessService
{
    private const PRODUCT_DETAILS_GROUP_NAME = 'Product Details';
    private const GRAPE_WINE_GROUP_NAME = 'Grape Wine Attributes';
    /**
     * @var AttributeCsvFileReadService
     */
    private AttributeCsvFileReadService $attributeCsvFileReadService;

    /**
     * @var CheckExistAttributeService
     */
    private CheckExistAttributeService $checkExistAttributeService;

    /**
     * @var CategorySetup
     */
    private CategorySetup $installer;

    /**
     * @var Attribute
     */
    private Attribute $attribute;

    /**
     * @var SaveAttributeOptionsService
     */
    private SaveAttributeOptionsService $saveAttributeOptionsService;

    /**
     * @var SaveOptionsToExistAttributeService
     */
    private SaveOptionsToExistAttributeService $saveOptionsToExistAttributeService;

    /**
     * @var Json
     */
    private Json $json;

    /**
     * @var AttributeGroupInterfaceFactory
     */
    private AttributeGroupInterfaceFactory $attributeGroupInterfaceFactory;

    /**
     * @var AttributeGroupRepositoryInterface
     */
    private AttributeGroupRepositoryInterface $attributeGroupRepository;

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @param AttributeCsvFileReadService $attributeCsvFileReadService
     * @param CheckExistAttributeService $checkExistAttributeService
     * @param CategorySetup $installer
     * @param Attribute $attribute
     * @param SaveAttributeOptionsService $saveAttributeOptionsService
     * @param SaveOptionsToExistAttributeService $saveOptionsToExistAttributeService
     * @param Json $json
     * @param AttributeGroupInterfaceFactory $attributeGroupInterfaceFactory
     * @param AttributeGroupRepositoryInterface $attributeGroupRepository
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        AttributeCsvFileReadService $attributeCsvFileReadService,
        CheckExistAttributeService $checkExistAttributeService,
        CategorySetup $installer,
        Attribute $attribute,
        SaveAttributeOptionsService $saveAttributeOptionsService,
        SaveOptionsToExistAttributeService $saveOptionsToExistAttributeService,
        Json $json,
        AttributeGroupInterfaceFactory $attributeGroupInterfaceFactory,
        AttributeGroupRepositoryInterface $attributeGroupRepository,
        ResourceConnection $resourceConnection
    ) {
        $this->attributeCsvFileReadService = $attributeCsvFileReadService;
        $this->checkExistAttributeService = $checkExistAttributeService;
        $this->installer = $installer;
        $this->attribute = $attribute;
        $this->saveAttributeOptionsService = $saveAttributeOptionsService;
        $this->saveOptionsToExistAttributeService = $saveOptionsToExistAttributeService;
        $this->json = $json;
        $this->attributeGroupInterfaceFactory = $attributeGroupInterfaceFactory;
        $this->attributeGroupRepository = $attributeGroupRepository;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function execute(): void
    {
        $this->createWineGroup();
        $attributeData = $this->attributeCsvFileReadService->execute();
        foreach ($attributeData as $attributeCode => $attributeData) {
            if ($attributeCode != 'header') {
                if ($this->checkGrapeAttribute($attributeData)) {
                    $groupId = self::GRAPE_WINE_GROUP_NAME;
                    $attributeData = $this->changeTypeGrapeWineAttribute($attributeData);
                } else {
                    $groupId = self::PRODUCT_DETAILS_GROUP_NAME;
                }

                $attributeIdStatus = $this->checkExistAttributeService->execute($attributeCode);
                if ($attributeIdStatus == false) {
                    $attribute = $this->saveNewAttribute($attributeData);
                    $attributeId = (int)$attribute->getId();
                    $this->saveAttributeOptionsService->execute($attribute, $attributeId, $attributeData['attribute_options']);
                } else {
                    $attributeSetNameArray = $this->attributeSetNameToArray($attributeData['attribute_set_name'], $attributeData['attribute_code']);
                    foreach ($attributeSetNameArray as $attributeSet) {
                        $this->addAttributetoGroup($attributeIdStatus, $attributeSet, $groupId);
                    }
                    $this->saveOptionsToExistAttributeService->execute($attributeCode, $attributeData, $attributeIdStatus);
                }
            }
        }
    }

    /**
     * @param array $attributeData
     * @return Attribute
     * @throws \Exception
     */
    private function saveNewAttribute(array $attributeData): Attribute
    {
        if ($this->checkGrapeAttribute($attributeData)) {
            $groupId = self::GRAPE_WINE_GROUP_NAME;
            $attributeData = $this->changeTypeGrapeWineAttribute($attributeData);
        } else {
            $groupId = self::PRODUCT_DETAILS_GROUP_NAME;
        }

        $this->attribute->setData(
            $attributeData
        );

        $this->attribute->save();

        $sortOrderAttribute = (int)$attributeData['sort_order'];
        $sortOrderAttribute = $sortOrderAttribute + 1;
        $attributeSetNameArray = $this->attributeSetNameToArray($attributeData['attribute_set_name'], $attributeData['attribute_code']);

        $attributeId = (int)$this->attribute->getId();
        foreach ($attributeSetNameArray as $attributeSet) {
            $this->addAttributetoGroup($attributeId, $attributeSet, $groupId, $sortOrderAttribute);
        }

        return $this->attribute;
    }

    /**
     * @param int $attributeId
     * @param string $attributeSetName
     * @param string $groupId
     * @param int|null $sortOrderAttribute
     * @return void
     */
    private function addAttributetoGroup(int $attributeId, string $attributeSetName, string $groupId, int $sortOrderAttribute = null): void
    {
        $this->installer->addAttributeToGroup(
            'catalog_product',
            $attributeSetName,
            $groupId,
            $attributeId,
            $sortOrderAttribute
        );
    }

    /**
     * @param string $jsonData
     * @param string $attributeCode
     * @return array
     */
    private function attributeSetNameToArray(string $jsonData, string $attributeCode): array
    {
        $attributeSetName = $this->json->unserialize($jsonData);

        if ($attributeCode == 'manufacturer') {
            $attributeSetName[] = 'Whisky';
            $attributeSetName[] = 'Spirit';
        }

        return $attributeSetName;
    }

    /**
     * @return void
     * @throws NoSuchEntityException
     * @throws StateException
     */
    private function createWineGroup(): void
    {
        $attributeGroup = $this->attributeGroupInterfaceFactory->create();
        $attributeSetId = $this->getAttributeSetIdWine();
        $attributeGroup->setAttributeSetId($attributeSetId);
        $attributeGroup->setAttributeGroupName(self::GRAPE_WINE_GROUP_NAME);
        $sortOrderNumber = $this->getProductDetailsGroupOrder($attributeSetId) + 1;
        $attributeGroup->setData('sort_order', $sortOrderNumber);

        $this->attributeGroupRepository->save($attributeGroup);
    }

    /**
     * @return int
     */
    private function getAttributeSetIdWine(): int
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select();

        $select->from('eav_attribute_set', 'attribute_set_id')->where('attribute_set_name = ?', 'Wine');

        return (int)$connection->fetchOne($select);
    }

    /**
     * @param array $attributeData
     * @return bool
     */
    private function checkGrapeAttribute(array $attributeData): bool
    {
        $attributeName = $attributeData['attribute_code'];
        $attributeName = explode('_', $attributeName);
        if ($attributeName[0] == 'grape') {
            return true;
        }

        return false;
    }

    /**
     * @param array $attributeData
     * @return array
     */
    private function changeTypeGrapeWineAttribute(array $attributeData): array
    {
        $attributeData['backend_type'] = 'varchar';
        $attributeData['frontend_input'] = 'text';
        $attributeData['frontend_class'] = 'validate-number';
        $attributeData['attribute_options'] = '[{"label":" ","value":""}]';
        $attributeData['source_model'] = '';

        return $attributeData;
    }

    /**
     * @param int $attributeId
     * @return int
     */
    private function getProductDetailsGroupOrder(int $attributeId): int
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select();

        $select->from('eav_attribute_group', 'sort_order')
            ->where('attribute_set_id = ?', $attributeId)
            ->where('attribute_group_name = ?', self::PRODUCT_DETAILS_GROUP_NAME);

        return (int)$connection->fetchOne($select);
    }
}
