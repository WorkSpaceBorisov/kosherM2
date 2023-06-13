<?php
declare(strict_types=1);

namespace Kosher\ProductAttributeSetExportImport\Service\AttributeSet;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

class SaveOptionsToExistAttributeService
{
    /**
     * @var ProductAttributeRepositoryInterface
     */
    private ProductAttributeRepositoryInterface $productAttribute;

    /**
     * @var SaveAttributeOptionsService
     */
    private SaveAttributeOptionsService $saveAttributeOptionsService;

    /**
     * @param ProductAttributeRepositoryInterface $productAttribute
     * @param SaveAttributeOptionsService $saveAttributeOptionsService
     */
    public function __construct(
        ProductAttributeRepositoryInterface $productAttribute,
        SaveAttributeOptionsService $saveAttributeOptionsService
    ) {
        $this->productAttribute = $productAttribute;
        $this->saveAttributeOptionsService = $saveAttributeOptionsService;
    }

    /**
     * @param string $attributeCode
     * @param array $attributeData
     * @param int $attributeId
     * @return void
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws StateException
     */
    public function execute(string $attributeCode, array $attributeData, int $attributeId): void
    {
        /** @var Attribute $attribute */
        $attribute = $this->productAttribute->get($attributeCode);
        $this->saveAttributeOptionsService->execute($attribute, $attributeId, $attributeData['attribute_options']);
    }
}
