<?php
declare(strict_types=1);

namespace Kosher\ProductAttributeSetExportImport\Service\AttributeSet;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Api\AttributeOptionManagementInterface;
use Magento\Eav\Api\Data\AttributeOptionLabelInterface;
use Magento\Eav\Model\Entity\Attribute\Option;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Serialize\Serializer\Json;

class SaveAttributeOptionsService
{
    /**
     * @var AttributeOptionManagementInterface
     */
    private AttributeOptionManagementInterface $attributeOptionManagement;

    /**
     * @var AttributeOptionLabelInterface
     */
    private AttributeOptionLabelInterface $attributeOptionLabel;

    /**
     * @var Option
     */
    private Option $option;

    /**
     * @var Json
     */
    private Json $json;

    /**
     * @param AttributeOptionManagementInterface $attributeOptionManagement
     * @param AttributeOptionLabelInterface $attributeOptionLabel
     * @param Option $option
     * @param Json $json
     */
    public function __construct(
        AttributeOptionManagementInterface $attributeOptionManagement,
        AttributeOptionLabelInterface $attributeOptionLabel,
        Option $option,
        Json $json
    ) {
        $this->attributeOptionManagement = $attributeOptionManagement;
        $this->attributeOptionLabel = $attributeOptionLabel;
        $this->option = $option;
        $this->json = $json;
    }

    /**
     * @param Attribute $attribute
     * @param int $attributeId
     * @param string $options
     * @return void
     * @throws InputException
     * @throws LocalizedException
     * @throws StateException
     */
    public function execute(Attribute $attribute, int $attributeId, string $options): void
    {
        $options = $this->json->unserialize($options);
        foreach ($options as $option) {
            if (!empty($option['value']) && !empty($option['label'])) {
                if ($attribute->getSource()->getOptionId($option['label']) == null) {
                    $this->option->setValue($option['value']);
                    $this->attributeOptionLabel->setStoreId(0);
                    $this->attributeOptionLabel->setLabel($option['label']);
                    $this->option->setLabel($this->attributeOptionLabel->getLabel());
                    $this->option->setStoreLabels([$this->attributeOptionLabel]);
                    $this->option->setSortOrder(0);
                    $this->option->setIsDefault(false);
                    $this->attributeOptionManagement->add('catalog_product', $attributeId, $this->option);
                }
            }
        }
    }
}
