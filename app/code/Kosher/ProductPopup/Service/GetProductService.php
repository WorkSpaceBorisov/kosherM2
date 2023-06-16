<?php

namespace Kosher\ProductPopup\Service;

use Exception;
use Kosher\ProductPopup\Api\PopupProductDataInterface;
use Kosher\ProductPopup\Service\ProductAttribute\GetOptionValueByIdService;
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
     * @param ProductRepositoryInterface $productRepository
     * @param Json $json
     * @param GetOptionValueByIdService $getOptionValueByIdService
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        Json $json,
        GetOptionValueByIdService $getOptionValueByIdService,
        StoreManagerInterface $storeManager
    ) {
        $this->productRepository = $productRepository;
        $this->json = $json;
        $this->getOptionValueByIdService = $getOptionValueByIdService;
        $this->storeManager = $storeManager;
    }

    /**
     * @param string $sku
     * @return array
     */
    public function get(string $sku): array
    {
        try {
            $product = $this->productRepository->get($sku);
            $storeId = $this->storeManager->getStore()->getId();
            $attributeData = [
                'manufacturer' => $product->getData('manufacturer'),
                'supervision' => $product->getData('supervision'),
                'halavi' => $product->getData('halavi'),
            ];

            $attributeData = $this->getOptionValueByIdService->execute($attributeData, $storeId);
            $product->addData($attributeData);

            return ['productData' => $product->getData(), 'status' => true];
        } catch (Exception $exception) {
            return ['message' => $exception->getMessage(), 'status' => false];
        }
    }
}
