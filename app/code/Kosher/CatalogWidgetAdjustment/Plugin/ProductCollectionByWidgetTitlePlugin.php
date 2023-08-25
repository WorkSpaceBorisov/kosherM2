<?php
declare(strict_types=1);

namespace Kosher\CatalogWidgetAdjustment\Plugin;

use Kosher\CatalogWidgetAdjustment\Service\ProductCollection\ProductCollectionByBlockWidgetTitleService;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogWidget\Block\Product\ProductsList;
use Magento\Framework\Exception\NoSuchEntityException;

class ProductCollectionByWidgetTitlePlugin
{
    /**
     * @var ProductCollectionByBlockWidgetTitleService
     */
    private ProductCollectionByBlockWidgetTitleService $productCollectionByBlockWidgetTitle;

    /**
     * @param ProductCollectionByBlockWidgetTitleService $productCollectionByBlockWidgetTitle
     */
    public function __construct(
        ProductCollectionByBlockWidgetTitleService $productCollectionByBlockWidgetTitle
    ) {
        $this->productCollectionByBlockWidgetTitle = $productCollectionByBlockWidgetTitle;
    }

    /**
     * @param ProductsList $subject
     * @param Collection $result
     * @return Collection
     * @throws NoSuchEntityException
     */
    public function afterCreateCollection(ProductsList $subject, Collection $result): Collection
    {
        $widgetBlockTitle = $subject->getTitle();
        if (!empty($widgetBlockTitle) &&
            ($widgetBlockTitle == ProductCollectionByBlockWidgetTitleService::SALE ||
            $widgetBlockTitle == ProductCollectionByBlockWidgetTitleService::NEW ||
            $widgetBlockTitle == ProductCollectionByBlockWidgetTitleService::HOLIDAYS_DEAL)
        ) {
            return $this->productCollectionByBlockWidgetTitle->execute($widgetBlockTitle);
        }

        return $result;
    }
}
