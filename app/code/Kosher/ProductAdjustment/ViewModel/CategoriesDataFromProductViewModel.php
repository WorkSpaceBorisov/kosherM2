<?php
declare(strict_types=1);

namespace Kosher\ProductAdjustment\ViewModel;

use Kosher\CategoryAdjustment\Service\CategoryAttribute\GetLabelCategoryAttributeFromProductService;
use Kosher\ProductAdjustment\Service\Attribute\CheckCurrentAndLastDateService;
use Kosher\ProductAdjustment\Service\ProductCollection\ProductCollectionWithCustomRequestService;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;

class CategoriesDataFromProductViewModel implements ArgumentInterface
{
    /**
     * @var GetLabelCategoryAttributeFromProductService
     */
    private GetLabelCategoryAttributeFromProductService $labelCategoryAttributeFromProductService;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var CheckCurrentAndLastDateService
     */
    private CheckCurrentAndLastDateService $checkCurrentAndLastDateService;

    /**
     * @param GetLabelCategoryAttributeFromProductService $labelCategoryAttributeFromProductService
     * @param StoreManagerInterface $storeManager
     * @param CheckCurrentAndLastDateService $checkCurrentAndLastDateService
     */
    public function __construct(
        GetLabelCategoryAttributeFromProductService $labelCategoryAttributeFromProductService,
        StoreManagerInterface                       $storeManager,
        CheckCurrentAndLastDateService              $checkCurrentAndLastDateService
    ) {
        $this->labelCategoryAttributeFromProductService = $labelCategoryAttributeFromProductService;
        $this->storeManager = $storeManager;
        $this->checkCurrentAndLastDateService = $checkCurrentAndLastDateService;
    }

    /**
     * @param ProductInterface $product
     * @return ProductInterface
     * @throws LocalizedException
     */
    public function getCategoryLabels(ProductInterface $product): ProductInterface
    {
        $storeId = (int)$this->storeManager->getStore()->getId();
        $categoryLabels['categoryLabels'] = $this->labelCategoryAttributeFromProductService->execute($product, $storeId);
        $product->addData($categoryLabels);

        return $product;
    }

    /**
     * @param array $requestParam
     * @param ProductInterface $product
     * @return bool
     */
    public function checkToDateAttributeProductByRequestParams(array $requestParam, ProductInterface $product): bool
    {
        if (!empty($requestParam[ProductCollectionWithCustomRequestService::REQUEST_PARAM])) {
            if ($requestParam[ProductCollectionWithCustomRequestService::REQUEST_PARAM] == ProductCollectionWithCustomRequestService::SALE) {
                $productAttribute = $product->getData(ProductCollectionWithCustomRequestService::SPECIAL_TO_DATE);
                $productAttributeFromDate = $product->getData(ProductCollectionWithCustomRequestService::SPECIAL_FROM_DATE);
                if (!empty($productAttribute)) return $this->checkToDateAttributeProduct($productAttribute);
                return true;
            }

            if ($requestParam[ProductCollectionWithCustomRequestService::REQUEST_PARAM] == ProductCollectionWithCustomRequestService::HOLIDAYS_DEAL) {
                $productAttribute = $product->getData(ProductCollectionWithCustomRequestService::HOLIDAYS_DEAL_TO);
                if (!empty($productAttribute)) return $this->checkToDateAttributeProduct($productAttribute);
                return true;
            }

            if ($requestParam[ProductCollectionWithCustomRequestService::REQUEST_PARAM] == ProductCollectionWithCustomRequestService::NEW) {
                $productAttribute = $product->getData(ProductCollectionWithCustomRequestService::NEWS_TO_DATE);
                if (!empty($productAttribute)) return $this->checkToDateAttributeProduct($productAttribute);
                return true;
            }
        }

        return true;
    }

    /**
     * @param ProductInterface $product
     * @param string|null $label
     * @return bool
     */
    public function checkLastDateForLabels(ProductInterface $product, string $label = null): bool
    {
        if (!empty($label)) {
            if ($label == ProductCollectionWithCustomRequestService::SALE) {
                $productAttribute = $product->getData(ProductCollectionWithCustomRequestService::SPECIAL_TO_DATE);
                $productAttributeFrom = $product->getData(ProductCollectionWithCustomRequestService::SPECIAL_FROM_DATE);
                $specialPrice = $product->getData(ProductCollectionWithCustomRequestService::SPECIAL_PRICE);
                if (!empty($specialPrice)) {
                    if (!empty($productAttribute) || !empty($productAttributeFrom)) {
                        if (!empty($productAttribute)) return $this->checkToDateAttributeProduct($productAttribute);
                        return true;
                    }

                    return true;
                }

                return false;
            }

            if ($label == ProductCollectionWithCustomRequestService::HOLIDAYS_DEAL) {
                $productAttribute = $product->getData(ProductCollectionWithCustomRequestService::HOLIDAYS_DEAL_TO);
                $productAttributeFrom = $product->getData(ProductCollectionWithCustomRequestService::HOLIDAYS_DEAL_FROM);
                if (!empty($productAttribute) || !empty($productAttributeFrom)) {
                    if (!empty($productAttribute)) return $this->checkToDateAttributeProduct($productAttribute);
                    return true;
                }

                return false;
            }

            if ($label == ProductCollectionWithCustomRequestService::NEW) {
                $productAttribute = $product->getData(ProductCollectionWithCustomRequestService::NEWS_TO_DATE);
                $productAttributeFrom = $product->getData(ProductCollectionWithCustomRequestService::NEWS_FROM_DATE);
                if (!empty($productAttribute) || !empty($productAttributeFrom)) {
                    if (!empty($productAttribute)) return $this->checkToDateAttributeProduct($productAttribute);
                    return true;
                }

                return false;
            }
        }

        return false;
    }

    /**
     * @param string $productToDate
     * @return bool
     */
    public function checkToDateAttributeProduct(string $productToDate): bool
    {
        return $this->checkCurrentAndLastDateService->execute($productToDate);
    }
}
