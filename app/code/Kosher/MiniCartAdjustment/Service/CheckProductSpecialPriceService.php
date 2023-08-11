<?php
declare(strict_types=1);

namespace Kosher\MiniCartAdjustment\Service;

use Kosher\ProductAdjustment\Service\ProductCollection\ProductCollectionWithCustomRequestService;
use Magento\Catalog\Api\Data\ProductInterface;
use Kosher\ProductAdjustment\Service\Attribute\CheckCurrentAndLastDateService;

class CheckProductSpecialPriceService
{
    /**
     * @var CheckCurrentAndLastDateService
     */
    private CheckCurrentAndLastDateService $checkToDateAttributeProduct;

    /**
     * @param CheckCurrentAndLastDateService $checkToDateAttributeProduct
     */
    public function __construct(
        CheckCurrentAndLastDateService $checkToDateAttributeProduct
    ) {
        $this->checkToDateAttributeProduct = $checkToDateAttributeProduct;
    }

    /**
     * @param ProductInterface $product
     * @return bool
     */
    public function execute(ProductInterface $product): bool
    {
        $productAttribute = $product->getData(ProductCollectionWithCustomRequestService::SPECIAL_TO_DATE);
        $productAttributeFrom = $product->getData(ProductCollectionWithCustomRequestService::SPECIAL_FROM_DATE);
        $specialPrice = $product->getData(ProductCollectionWithCustomRequestService::SPECIAL_PRICE);
        if (!empty($specialPrice)) {
            if (!empty($productAttribute) || !empty($productAttributeFrom)) {
                if (!empty($productAttribute)) return $this->checkToDateAttributeProduct->execute($productAttribute);
                return true;
            }

            return true;
        }

        return false;
    }
}
