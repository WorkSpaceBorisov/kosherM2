<?php
declare(strict_types=1);

namespace Kosher\CategoryAdjustment\ViewModel;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use Kosher\CategoryAdjustment\Query\GetCategoryIconPathQuery;
use Magento\Catalog\Api\Data\CategoryInterface;

class ProductNavigationViewModel implements ArgumentInterface
{
    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var CategoryRepositoryInterface
     */
    private CategoryRepositoryInterface $categoryRepository;

    /**
     * @var GetCategoryIconPathQuery
     */
    private GetCategoryIconPathQuery $categoryIconPathQuery;

    /**
     * @param StoreManagerInterface $storeManager
     * @param CategoryRepositoryInterface $categoryRepository
     * @param GetCategoryIconPathQuery $categoryIconPathQuery
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CategoryRepositoryInterface $categoryRepository,
        GetCategoryIconPathQuery $categoryIconPathQuery
    ) {
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->categoryIconPathQuery = $categoryIconPathQuery;
    }

    /**
     * @return Category[]|Collection
     * @throws NoSuchEntityException
     */
    public function getTopCategoriesForStore(): array|Collection
    {
        $rootCategoryId = $this->storeManager->getStore()->getRootCategoryId();
        $rootCategory = $this->categoryRepository->get($rootCategoryId);

        return $rootCategory->getChildrenCategories();
    }

    /**
     * @param CategoryInterface $category
     * @return string|null
     */
    public function getIconPathForCategory(CategoryInterface $category): ?string
    {
        $categoryAttributeId = (int)$category->getAttributes()['category_icon']->getAttributeId();
        $categoryId = (int)$category->getEntityId();

        return $this->categoryIconPathQuery->execute($categoryAttributeId, $categoryId);
    }
}
