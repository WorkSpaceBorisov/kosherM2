<?php
declare(strict_types=1);

namespace Kosher\ProductPopup\ViewModel;

use Kosher\ProductPopup\Api\PopupProductDataInterface;
use Magento\Catalog\Helper\Data;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;

class GetCurretProductSkuViewModel implements ArgumentInterface
{
    /**
     * @var Data
     */
    private Data $productHelper;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @param Data $productHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Data $productHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->productHelper = $productHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * @return string|null
     */
    public function getProductSku(): ?string
    {
        $product = $this->productHelper->getProduct();

        return $product?->getSku();
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getStoreUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }

    /**
     * @return string
     */
    public function getProductBySkuRequest(): string
    {
        return PopupProductDataInterface::API_PREFIX . PopupProductDataInterface::GET_PRODUCT_BY_SKU_REQUEST;
    }
}
