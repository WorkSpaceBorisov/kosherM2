<?php
declare(strict_types=1);

namespace Kosher\ProductAttributeSetExportImport\Service\AttributeSet;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Setup\CategorySetup;

class AttributeSetSaveProcessService
{
    private int $attributeId;

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
     * @param AttributeCsvFileReadService $attributeCsvFileReadService
     * @param CheckExistAttributeService $checkExistAttributeService
     * @param CategorySetup $installer
     * @param Attribute $attribute
     * @param SaveAttributeOptionsService $saveAttributeOptionsService
     * @param SaveOptionsToExistAttributeService $saveOptionsToExistAttributeService
     */
    public function __construct(
        AttributeCsvFileReadService $attributeCsvFileReadService,
        CheckExistAttributeService $checkExistAttributeService,
        CategorySetup $installer,
        Attribute $attribute,
        SaveAttributeOptionsService $saveAttributeOptionsService,
        SaveOptionsToExistAttributeService $saveOptionsToExistAttributeService
    ) {
        $this->attributeCsvFileReadService = $attributeCsvFileReadService;
        $this->checkExistAttributeService = $checkExistAttributeService;
        $this->installer = $installer;
        $this->attribute = $attribute;
        $this->saveAttributeOptionsService = $saveAttributeOptionsService;
        $this->saveOptionsToExistAttributeService = $saveOptionsToExistAttributeService;
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
                    $this->attributeId = (int)$attribute->getId();
                    $this->saveAttributeOptionsService->execute($attribute, $this->attributeId, $attributeData['attribute_options']);
                } else {
                    $this->saveOptionsToExistAttributeService->execute($attributeCode, $attributeData, $attributeIdStatus);
                }
            }

            $i++;
        }
    }

    /**
     * @param array $attributeData
     * @param int $i
     * @return Attribute
     * @throws \Exception
     */
    private function saveNewAttribute(array $attributeData, int $i): Attribute
    {
        $this->attribute->setData(
            $attributeData
        );

        $this->attribute->save();

        $this->installer->addAttributeToGroup('catalog_product', $attributeData['attribute_set_name'], 'Product Details', $this->attribute->getId());

        return $this->attribute;
    }
}
