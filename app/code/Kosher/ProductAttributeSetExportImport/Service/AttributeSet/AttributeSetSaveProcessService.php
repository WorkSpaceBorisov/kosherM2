<?php
declare(strict_types=1);

namespace Kosher\ProductAttributeSetExportImport\Service\AttributeSet;

use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Eav\Api\Data\AttributeInterfaceFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\Api\DataObjectHelper;

class AttributeSetSaveProcessService
{
    private string $attributeGroupName = '';
    private string $attributeSetName = '';
    private int $attributeId;
    private int $attributeGroupId;
    private int $attributeSetId;
    private AttributeCsvFileReadService $attributeCsvFileReadService;
    private CheckExistAttributeService $checkExistAttributeService;
    private DataObjectHelper $dataObjectHelper;
    private AttributeInterfaceFactory $attributeInterfaceFactory;
    private Attribute $attribute;
    private GetAttributeSetIdService $getAttributeSetIdService;
    private CheckExistGroupByAttributeSetIdService $checkExistGroupByAttributeSetIdService;
    private AssignAttributeSetToGroupService $assignAttributeSetToGroupService;
    private SaveEavEntityAttributeDataService $saveEavEntityAttributeDataService;
    private SaveCatalogEavAttributeService $saveCatalogEavAttributeService;
    private SaveAttributeOptionsService $saveAttributeOptionsService;

    public function __construct(
        AttributeCsvFileReadService $attributeCsvFileReadService,
        CheckExistAttributeService $checkExistAttributeService,
        DataObjectHelper $dataObjectHelper,
        AttributeInterfaceFactory $attributeInterfaceFactory,
        Attribute $attribute,
        GetAttributeSetIdService $getAttributeSetIdService,
        CheckExistGroupByAttributeSetIdService $checkExistGroupByAttributeSetIdService,
        AssignAttributeSetToGroupService $assignAttributeSetToGroupService,
        SaveEavEntityAttributeDataService $saveEavEntityAttributeDataService,
        SaveCatalogEavAttributeService $saveCatalogEavAttributeService,
        SaveAttributeOptionsService $saveAttributeOptionsService
    ) {
        $this->attributeCsvFileReadService = $attributeCsvFileReadService;
        $this->checkExistAttributeService = $checkExistAttributeService;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->attributeInterfaceFactory = $attributeInterfaceFactory;
        $this->attribute = $attribute;
        $this->getAttributeSetIdService = $getAttributeSetIdService;
        $this->checkExistGroupByAttributeSetIdService = $checkExistGroupByAttributeSetIdService;
        $this->assignAttributeSetToGroupService = $assignAttributeSetToGroupService;
        $this->saveEavEntityAttributeDataService = $saveEavEntityAttributeDataService;
        $this->saveCatalogEavAttributeService = $saveCatalogEavAttributeService;
        $this->saveAttributeOptionsService = $saveAttributeOptionsService;
    }

    public function execute()
    {
        $attributeData = $this->attributeCsvFileReadService->execute();
        foreach ($attributeData as $attributeCode => $attributeData) {
            if ($attributeCode != 'header') {
                $attributeIdStatus = $this->checkExistAttributeService->execute($attributeCode);
                $this->attributeId = $attributeIdStatus ?: $this->saveNewAttribute($attributeData);

                if ($this->attributeSetName != $attributeData['attribute_set_name']) {
                    $this->attributeSetName = $attributeData['attribute_set_name'];
                    $this->attributeSetId = $this->getAttributeSetIdService->execute($this->attributeSetName);
                }
                if ($this->attributeGroupName != $attributeData['attribute_group_name']) {
                    $this->attributeGroupName = $attributeData['attribute_group_name'];
                    $attributeGroupCheck = $this->checkExistGroupByAttributeSetIdService->execute('Product Details', $this->attributeSetId);
                    $this->attributeGroupId = $attributeGroupCheck ?: $this->assignAttributeSetToGroupService->execute('Product Details', $this->attributeSetId);
                }

                if (!$attributeIdStatus) {
                    $this->saveEavEntityAttributeDataService->execute($this->attributeSetId, $this->attributeGroupId, $this->attributeId);
                    $this->saveAttributeOptionsService->execute($attributeCode, $attributeData['attribute_options']);
                }
            }
        }
    }

    private function saveNewAttribute(array $attributeData)
    {
        /** @var AttributeInterface $eavAttribute */
        $eavAttribute = $this->attributeInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $eavAttribute,
            $attributeData,
            AttributeInterface::class
        );

        $this->attribute->save($eavAttribute);
        $this->saveCatalogEavAttributeService->execute($attributeData);

        return (int)$eavAttribute->getAttributeId();
    }
}
