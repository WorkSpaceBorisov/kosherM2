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
    private const CART_ITEM_NEWS_FROM_DATE = 'news_from_date';
    private const CART_ITEM_SPECIAL_PRICE = 'special_price';
    private const CART_ITEM_OLD_PRICE = 'price';

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
        $new = $product->getData(self::CART_ITEM_NEWS_FROM_DATE);
        $specialprice = $product->getData(self::CART_ITEM_SPECIAL_PRICE);
        $oldprice = $product->getData(self::CART_ITEM_OLD_PRICE);
        
        if (!empty($singleweight)) {
            $result[self::CART_ITEM_SINGLEWEIGHT] = $singleweight;
        }
        if (!empty($new)) {
            $result[self::CART_ITEM_NEWS_FROM_DATE] = $new;
        }
        if (!empty($specialprice)) {
            $result[self::CART_ITEM_SPECIAL_PRICE] = $specialprice;
        }
        if (!empty($oldprice)) {
            $result[self::CART_ITEM_OLD_PRICE] = $oldprice;
        }

        return $result;
    }
}
