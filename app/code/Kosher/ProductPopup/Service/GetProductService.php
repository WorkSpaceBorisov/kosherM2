<?php
declare(strict_types=1);

namespace Kosher\ProductPopup\Service;

use Exception;
use Kosher\CategoryAdjustment\Service\CategoryAttribute\GetLabelCategoryAttributeFromProductService;
use Kosher\ProductPopup\Api\PopupProductDataInterface;
use Kosher\ProductPopup\Service\ProductAttribute\GetOptionValueByIdService;
use Kosher\ProductPopup\Service\ProductAttribute\GetResizeImageCacheUrlService;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;

class GetProductService implements PopupProductDataInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @var Json
     */
    private Json $json;

    /**
     * @var GetOptionValueByIdService
     */
    private GetOptionValueByIdService $getOptionValueByIdService;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var GetResizeImageCacheUrlService
     */
    private GetResizeImageCacheUrlService $getResizeImageCacheUrlService;

    /**
     * @var GetLabelCategoryAttributeFromProductService
     */
    private GetLabelCategoryAttributeFromProductService $labelCategoryAttributeFromProductService;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param Json $json
     * @param GetOptionValueByIdService $getOptionValueByIdService
     * @param StoreManagerInterface $storeManager
     * @param GetResizeImageCacheUrlService $getResizeImageCacheUrlService
     * @param GetLabelCategoryAttributeFromProductService $labelCategoryAttributeFromProductService
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        Json $json,
        GetOptionValueByIdService $getOptionValueByIdService,
        StoreManagerInterface $storeManager,
        GetResizeImageCacheUrlService $getResizeImageCacheUrlService,
        GetLabelCategoryAttributeFromProductService $labelCategoryAttributeFromProductService
    ) {
        $this->productRepository = $productRepository;
        $this->json = $json;
        $this->getOptionValueByIdService = $getOptionValueByIdService;
        $this->storeManager = $storeManager;
        $this->getResizeImageCacheUrlService = $getResizeImageCacheUrlService;
        $this->labelCategoryAttributeFromProductService = $labelCategoryAttributeFromProductService;
    }

    /**
     * @param string $sku
     * @return array
     */
    public function get(string $sku): array
    {
        try {
            $product = $this->productRepository->get($sku);
            $storeId = (int)$this->storeManager->getStore()->getId();
            $categoryLabels['categoryLabels'] = $this->labelCategoryAttributeFromProductService->execute($product, $storeId);
            $attributeData = [
                'manufacturer' => $product->getData('manufacturer'),
                'supervision' => $product->getData('supervision'),
                'halavi' => $product->getData('halavi'),
                'color' => $product->getData('color'),
                'size' => $product->getData('size'),
                'alcvol' => $product->getData('alcvol')
            ];

            $attributeData = $this->getOptionValueByIdService->execute($attributeData, $storeId);
            $resizeImages = $this->getResizeImageCacheUrlService->execute($product);
            $product->addData($resizeImages);
            $product->addData($attributeData);
            $product->addData($categoryLabels);

            return ['productData' => $product->getData(), 'status' => true];
        } catch (Exception $exception) {
            return ['message' => $exception->getMessage(), 'status' => false];
        }
    }
}
