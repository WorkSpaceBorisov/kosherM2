<?php
declare(strict_types=1);

namespace Kosher\MiniCartAdjustment\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\CustomerData\AbstractItem;
use Magento\CurrencySymbol\Model\System\Currencysymbol;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Model\StoreManagerInterface;
use Kosher\MiniCartAdjustment\Service\CheckProductSpecialPriceService;

class SetDefaultItemsForMinicartPlugin
{
    private const CART_ITEM_SINGLEWEIGHT = 'singleweight';
    private const CART_ITEM_NEWS_FROM_DATE = 'news_from_date';
    private const CART_ITEM_SPECIAL_PRICE = 'special_price';
    private const CART_ITEM_OLD_PRICE = 'price';
    private const CART_ITEM_CURRENCY = 'currency_symbol';

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @var Currencysymbol
     */
    private Currencysymbol $currencySymbol;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var CheckProductSpecialPriceService
     */
    private CheckProductSpecialPriceService $checkProductSpecialPriceService;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param Currencysymbol $currencySymbol
     * @param StoreManagerInterface $storeManager
     * @param CheckProductSpecialPriceService $checkProductSpecialPriceService
     */
    public function __construct(
        ProductRepositoryInterface      $productRepository,
        Currencysymbol                  $currencySymbol,
        StoreManagerInterface           $storeManager,
        CheckProductSpecialPriceService $checkProductSpecialPriceService
    )
    {
        $this->productRepository = $productRepository;
        $this->currencySymbol = $currencySymbol;
        $this->storeManager = $storeManager;
        $this->checkProductSpecialPriceService = $checkProductSpecialPriceService;
    }

    /**
     * @param AbstractItem $subject
     * @param $result
     * @param Item $item
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterGetItemData(AbstractItem $subject, $result, Item $item): array
    {
        $productSku = $item->getProduct()->getSku();
        $product = $this->productRepository->get($productSku);
        $singleweight = $product->getData(self::CART_ITEM_SINGLEWEIGHT);
        $newFromDate = $product->getData(self::CART_ITEM_NEWS_FROM_DATE);
        $checkPrice = $this->checkProductSpecialPriceService->execute($product);
        if ($checkPrice) {
            $result[self::CART_ITEM_SPECIAL_PRICE] = $product->getData(self::CART_ITEM_SPECIAL_PRICE);
            $result[self::CART_ITEM_OLD_PRICE] = $product->getPrice();;
        }

        if (!empty($singleweight)) {
            $result[self::CART_ITEM_SINGLEWEIGHT] = $singleweight;
        }

        if (!empty($newFromDate)) {
            $result[self::CART_ITEM_NEWS_FROM_DATE] = $newFromDate;
        }

        $result[self::CART_ITEM_CURRENCY] = $this->currencySymbolForStore();


        return $result;
    }

    /**
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function currencySymbolForStore(): string
    {
        $currentCurrency = $this->storeManager->getStore()->getCurrentCurrency()->getCurrencyCode();
        $symbolsData = $this->currencySymbol->getCurrencySymbolsData();

        return $symbolsData[$currentCurrency]['displaySymbol'];
    }
}
