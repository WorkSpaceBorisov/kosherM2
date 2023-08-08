<?php
declare(strict_types=1);

namespace Kosher\ProductAdjustment\Plugin;

use Kosher\ProductAdjustment\Service\ProductCollection\ProductCollectionWithCustomRequestService;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer\ItemCollectionProviderInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;

class ProductCollectionWithCustomRequestPlugin
{
    /**
     * @var ProductCollectionWithCustomRequestService
     */
    private ProductCollectionWithCustomRequestService $collectionWithCustomRequestService;

    /**
     * @param ProductCollectionWithCustomRequestService $collectionWithCustomRequestService
     */
    public function __construct(
        ProductCollectionWithCustomRequestService $collectionWithCustomRequestService
    ) {
        $this->collectionWithCustomRequestService = $collectionWithCustomRequestService;
    }

    /**
     * @param ItemCollectionProviderInterface $subject
     * @param Collection $result
     * @param Category $category
     * @return Collection
     */
    public function afterGetCollection(ItemCollectionProviderInterface $subject, Collection $result, Category $category): Collection
    {
        return $this->collectionWithCustomRequestService->execute($result);
    }
}
