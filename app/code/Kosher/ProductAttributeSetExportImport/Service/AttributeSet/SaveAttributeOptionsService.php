<?php
declare(strict_types=1);

namespace Kosher\ProductAttributeSetExportImport\Service\AttributeSet;

use Magento\Eav\Api\AttributeOptionManagementInterface;
use Magento\Eav\Api\Data\AttributeOptionInterface;
use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory;
use Magento\Framework\Serialize\Serializer\Json;

class SaveAttributeOptionsService
{
    private AttributeOptionManagementInterface $attributeOptionManagement;
    private Json $json;
    private AttributeOptionInterfaceFactory $attributeOptionInterfaceFactory;

    public function __construct(
        AttributeOptionManagementInterface $attributeOptionManagement,
        AttributeOptionInterfaceFactory $attributeOptionInterfaceFactory,
        Json $json
    ) {
        $this->attributeOptionManagement = $attributeOptionManagement;
        $this->json = $json;
        $this->attributeOptionInterfaceFactory = $attributeOptionInterfaceFactory;
    }

    public function execute(string $attributeCode, string $options)
    {
        $options = $this->json->unserialize($options);
        foreach ($options as $option) {
            if (!empty($option['value']) && !empty($option['label'])) {
                $opt = $this->attributeOptionInterfaceFactory->create();
                /** @var AttributeOptionInterface $opt */
                $opt->setValue($option['value']);
                $opt->setLabel($option['label']);
                $this->attributeOptionManagement->add(4, $attributeCode, $opt);
            }
        }
    }
}
