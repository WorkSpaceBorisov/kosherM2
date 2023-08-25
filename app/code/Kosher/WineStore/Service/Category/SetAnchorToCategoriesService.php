<?php
declare(strict_types=1);

namespace Kosher\WineStore\Service\Category;

use Exception;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class SetAnchorToCategoriesService
{
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    private CategoryRepositoryInterface $categoryRepository;

    /**
     * @param CollectionFactory $collectionFactory
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        CategoryRepositoryInterface $categoryRepository
    ){
        $this->collectionFactory = $collectionFactory;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function execute(): void
    {
        try {
            $categoryCollection = $this->collectionFactory->create();
            $categoryCollection->addAttributeToSelect('*');
            foreach ($categoryCollection as $item) {
                if (!empty($item->getIsActive())) {
                    $item->setIsAnchor(true);
                    $this->categoryRepository->save($item);
                }
            }
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}
