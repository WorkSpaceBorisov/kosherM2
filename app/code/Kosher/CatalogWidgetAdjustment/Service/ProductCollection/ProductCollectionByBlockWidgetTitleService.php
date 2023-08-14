<?php
declare(strict_types=1);

namespace Kosher\CatalogWidgetAdjustment\Service\ProductCollection;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use PharIo\Manifest\ElementCollectionException;

class ProductCollectionByBlockWidgetTitleService
{
    const SALE = 'Sale';
    const SPECIAL_PRICE = 'special_price';
    const SPECIAL_TO_DATE = 'special_to_date';
    const SPECIAL_FROM_DATE = 'special_from_date';
    const HOLIDAYS_DEAL = 'Special';
    const HOLIDAYS_DEAL_FROM = 'dv_deal_from';
    const HOLIDAYS_DEAL_TO = 'dv_deal_to';
    const NEW = 'New';
    const NEWS_FROM_DATE = 'news_from_date';
    const NEWS_TO_DATE = 'news_to_date';

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $productCollection;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @param CollectionFactory $productCollection
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CollectionFactory $productCollection,
        StoreManagerInterface $storeManager
    ) {
        $this->productCollection = $productCollection;
        $this->storeManager = $storeManager;
    }

    /**
     * @param string $blockWidgetTitle
     * @return Collection
     * @throws NoSuchEntityException
     */
    public function execute(string $blockWidgetTitle): Collection
    {
        $storeId = (int)$this->storeManager->getStore()->getId();
        $productCollection = $this->productCollection->create();
        $productCollection->addAttributeToSelect('*');
        $productCollection->setPageSize(20);
        $productCollection->addStoreFilter($storeId);
        try {
            if ($blockWidgetTitle == self::SALE)
                $productCollection->addAttributeToFilter(self::SPECIAL_PRICE, ['notnull' => true])
                    ->addAttributeToFilter(self::SPECIAL_FROM_DATE, ['notnull' => true])
                    ->addAttributeToFilter(self::SPECIAL_TO_DATE, ['null' => true]);

            if ($blockWidgetTitle == self::HOLIDAYS_DEAL)
                $productCollection->addAttributeToFilter(self::HOLIDAYS_DEAL_FROM, ['notnull' => true])
                    ->addAttributeToFilter(self::HOLIDAYS_DEAL_TO, ['null' => true]);


            if ($blockWidgetTitle == self::NEW)
                $productCollection->addAttributeToFilter(self::NEWS_FROM_DATE, ['notnull' => true])
                ->addAttributeToFilter(self::NEWS_TO_DATE, ['null' => true]);
        } catch (\Exception $e) {
            throw new ElementCollectionException($e->getMessage());
        }

        return $productCollection;
    }
}
