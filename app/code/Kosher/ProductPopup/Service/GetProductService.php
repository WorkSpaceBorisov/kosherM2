<?php
declare(strict_types=1);

namespace Kosher\ProductPopup\Service;

use Exception;
use Kosher\ProductPopup\Api\PopupProductDataInterface;
use Kosher\ProductPopup\Service\ProductAttribute\GetOptionValueByIdService;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;
use Kosher\ProductPopup\Service\ProductAttribute\GetResizeImageCacheUrlService;

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
     * @param ProductRepositoryInterface $productRepository
     * @param Json $json
     * @param GetOptionValueByIdService $getOptionValueByIdService
     * @param StoreManagerInterface $storeManager
     * @param GetResizeImageCacheUrlService $getResizeImageCacheUrlService
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        Json $json,
        GetOptionValueByIdService $getOptionValueByIdService,
        StoreManagerInterface $storeManager,
        GetResizeImageCacheUrlService $getResizeImageCacheUrlService
    ) {
        $this->productRepository = $productRepository;
        $this->json = $json;
        $this->getOptionValueByIdService = $getOptionValueByIdService;
        $this->storeManager = $storeManager;
        $this->getResizeImageCacheUrlService = $getResizeImageCacheUrlService;
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
            $attributeData = [
                'manufacturer' => $product->getData('manufacturer'),
                'supervision' => $product->getData('supervision'),
                'halavi' => $product->getData('halavi'),
            ];

            $attributeData = $this->getOptionValueByIdService->execute($attributeData, $storeId);
            $resizeImages = $this->getResizeImageCacheUrlService->execute($product);
            $product->addData($resizeImages);
            $product->addData($attributeData);

            return ['productData' => $product->getData(), 'status' => true];
        } catch (Exception $exception) {
            return ['message' => $exception->getMessage(), 'status' => false];
        }
    }
}
