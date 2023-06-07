<?php

namespace Kosher\ProductPopup\Service;

use Exception;
use Kosher\ProductPopup\Api\PopupProductDataInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Serialize\Serializer\Json;

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

    public function __construct(
        ProductRepositoryInterface $productRepository,
        Json $json
    ) {
        $this->productRepository = $productRepository;
        $this->json = $json;
    }

    /**
     * @param string $sku
     * @return string
     */
    public function get(string $sku): string
    {
        try {
            $product = $this->productRepository->get($sku);
            $productData = $product->getData();

            return $this->json->serialize(['productData' => $productData, 'status' => true]);
        } catch (Exception $exception) {
            return $this->json->serialize(['message' => $exception->getMessage(), 'status' => false]);
        }
    }
}
