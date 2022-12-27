<?php
declare(strict_types=1);

namespace Kosher\WineStore\Service;

use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Exception\LocalizedException;

class AddAttributeServiceService
{
    /**
     * @var AttributeSetFactory
     */
    private AttributeSetFactory $attributeSetFactory;

    /**
     * @var CategorySetupFactory
     */
    private CategorySetupFactory $categorySetupFactory;

    /**
     * @param AttributeSetFactory $attributeSetFactory
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        AttributeSetFactory $attributeSetFactory,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->attributeSetFactory = $attributeSetFactory;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * @param string $attributeName
     * @return void
     * @throws LocalizedException
     */
    public function execute(string $attributeName): void
    {
        $categorySetup = $this->categorySetupFactory->create();

        $attributeSet = $this->attributeSetFactory->create();
        $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);
        $data = [
            'attribute_set_name' => $attributeName,
            'entity_type_id' => $entityTypeId,
            'sort_order' => 1,
        ];
        $attributeSet->setData($data);
        $attributeSet->validate();
        $attributeSet->save();
        $attributeSet->initFromSkeleton($attributeSetId);
        $attributeSet->save();
    }
}
