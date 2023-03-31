<?php
declare(strict_types=1);

namespace Kosher\ProductAttributeSetExportImport\Service\AttributeSet;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Framework\Serialize\Serializer\Json;

class AttributeSetSaveProcessService
{

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
     * @param AttributeCsvFileReadService $attributeCsvFileReadService
     * @param CheckExistAttributeService $checkExistAttributeService
     * @param CategorySetup $installer
     * @param Attribute $attribute
     * @param SaveAttributeOptionsService $saveAttributeOptionsService
     * @param SaveOptionsToExistAttributeService $saveOptionsToExistAttributeService
     * @param Json $json
     */
    public function __construct(
        AttributeCsvFileReadService $attributeCsvFileReadService,
        CheckExistAttributeService $checkExistAttributeService,
        CategorySetup $installer,
        Attribute $attribute,
        SaveAttributeOptionsService $saveAttributeOptionsService,
        SaveOptionsToExistAttributeService $saveOptionsToExistAttributeService,
        Json $json
    ) {
        $this->attributeCsvFileReadService = $attributeCsvFileReadService;
        $this->checkExistAttributeService = $checkExistAttributeService;
        $this->installer = $installer;
        $this->attribute = $attribute;
        $this->saveAttributeOptionsService = $saveAttributeOptionsService;
        $this->saveOptionsToExistAttributeService = $saveOptionsToExistAttributeService;
        $this->json = $json;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function execute(): void
    {
        $attributeData = $this->attributeCsvFileReadService->execute();
        $i = 1;
        foreach ($attributeData as $attributeCode => $attributeData) {
            if ($attributeCode != 'header') {
                $attributeIdStatus = $this->checkExistAttributeService->execute($attributeCode);
                if ($attributeIdStatus == false) {
                    $attribute = $this->saveNewAttribute($attributeData, $i);
                    $attributeId = (int)$attribute->getId();
                    $this->saveAttributeOptionsService->execute($attribute, $attributeId, $attributeData['attribute_options']);
                } else {
                    $attributeSetNameArray = $this->attributeSetNameToArray($attributeData['attribute_set_name'], $attributeData['attribute_code']);
                    foreach ($attributeSetNameArray as $attributeSet) {
                        $this->addAttributetoGroup($attributeIdStatus, $attributeSet);
                    }
                    $this->saveOptionsToExistAttributeService->execute($attributeCode, $attributeData, $attributeIdStatus);
                }
            }

            $i++;
        }
    }

    /**
     * @param array $attributeData
     * @return Attribute
     * @throws \Exception
     */
    private function saveNewAttribute(array $attributeData): Attribute
    {
        $this->attribute->setData(
            $attributeData
        );

        $this->attribute->save();

        $sortOrderAttribute = (int)$attributeData['sort_order'];
        $sortOrderAttribute = $sortOrderAttribute + 1;
        $attributeSetNameArray = $this->attributeSetNameToArray($attributeData['attribute_set_name'], $attributeData['attribute_code']);

        $attributeId = (int)$this->attribute->getId();
        foreach ($attributeSetNameArray as $attributeSet) {
            $this->addAttributetoGroup($attributeId, $attributeSet, $sortOrderAttribute);
        }

        return $this->attribute;
    }

    /**
     * @param int $attributeId
     * @param string $attributeSetName
     * @param int|null $sortOrderAttribute
     * @return void
     */
    private function addAttributetoGroup(int $attributeId, string $attributeSetName, int $sortOrderAttribute = null): void
    {
        $this->installer->addAttributeToGroup(
            'catalog_product',
            $attributeSetName,
            'Product Details',
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
}
