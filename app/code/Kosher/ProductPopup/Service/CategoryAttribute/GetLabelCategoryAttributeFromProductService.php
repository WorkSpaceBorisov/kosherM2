<?php
declare(strict_types=1);

namespace Kosher\ProductPopup\Service\CategoryAttribute;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;

class GetLabelCategoryAttributeFromProductService
{
    private const CATEGORY_LABEL = 'category_label';

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param ProductInterface $product
     * @param int $storeId
     * @return array
     * @throws LocalizedException
     */
    public function execute(ProductInterface $product, int $storeId): array
    {
        $result = [];
        $categoryIds = $product->getCategoryIds();
        $categories = $this->getCategoryCollection($storeId)->addAttributeToFilter('entity_id', $categoryIds);
        foreach ($categories as $category) {
            $categoryLabelImagePath = $category->getData(self::CATEGORY_LABEL);
            if (!in_array($categoryLabelImagePath, $result) && !empty($categoryLabelImagePath)) {
                $result[] = $categoryLabelImagePath;
            }
        }

        return $result;
    }

    /**
     * @param int $storeId
     * @return Collection
     * @throws LocalizedException
     */
    private function getCategoryCollection(int $storeId): Collection
    {
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect('*')
        ->setStore($storeId);

        return $collection;
    }
}
