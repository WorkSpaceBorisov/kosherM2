<?php
declare(strict_types=1);

namespace Kosher\ProductAdjustment\ViewModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Kosher\CategoryAdjustment\Service\CategoryAttribute\GetLabelCategoryAttributeFromProductService;
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
     * @param GetLabelCategoryAttributeFromProductService $labelCategoryAttributeFromProductService
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        GetLabelCategoryAttributeFromProductService $labelCategoryAttributeFromProductService,
        StoreManagerInterface $storeManager
    ) {
        $this->labelCategoryAttributeFromProductService = $labelCategoryAttributeFromProductService;
        $this->storeManager = $storeManager;
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
}
