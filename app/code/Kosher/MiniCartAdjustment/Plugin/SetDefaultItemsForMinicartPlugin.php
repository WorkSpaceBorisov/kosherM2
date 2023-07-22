<?php
declare(strict_types=1);

namespace Kosher\MiniCartAdjustment\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\CustomerData\AbstractItem;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Item;

class SetDefaultItemsForMinicartPlugin
{
    private const CART_ITEM_SINGLEWEIGHT = 'singleweight';

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        ProductRepositoryInterface $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * @param AbstractItem $subject
     * @param $result
     * @param Item $item
     * @return array
     * @throws NoSuchEntityException
     */
    public function afterGetItemData(AbstractItem $subject, $result, Item $item): array
    {
        $productSku = $item->getProduct()->getSku();
        $product = $this->productRepository->get($productSku);
        $singleweight = $product->getData(self::CART_ITEM_SINGLEWEIGHT);
        if (!empty($singleweight)) {
            $result[self::CART_ITEM_SINGLEWEIGHT] = $singleweight;
        }

        return $result;
    }
}
