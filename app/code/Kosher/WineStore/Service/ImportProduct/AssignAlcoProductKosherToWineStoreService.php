<?php
declare(strict_types=1);

namespace Kosher\WineStore\Service\ImportProduct;

use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\ResourceConnection;

class AssignAlcoProductKosherToWineStoreService
{
    private const CATEGORY_NAME = 'Wine & Spirits';
    private const DESTINATION_TABLE = 'catalog_product_website';

    /**
     * @var CategoryFactory
     */
    private CategoryFactory $categoryFactory;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @param CategoryFactory $categoryFactory
     * @param CollectionFactory $collectionFactory
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        CategoryFactory $categoryFactory,
        CollectionFactory $collectionFactory,
        ResourceConnection $resourceConnection
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->collectionFactory = $collectionFactory;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        $productIds = $this->getProductCollection();
        $data = $this->prepareDataToSave($productIds);
        $this->saveData($data);
    }

    /**
     * @return void
     */
    public function getCategoryIdByName()
    {
        $collection = $this->categoryFactory->create()
            ->getCollection()
            ->addAttributeToFilter('name', self::CATEGORY_NAME)
            ->setPageSize(1);

        if ($collection->getSize()) {
            return $collection->getFirstItem()->getId();
        }
    }

    /**
     * @return array
     */
    public function getProductCollection(): array
    {
        $websiteIds = [1,2];
        $categoryId = $this->getCategoryIdByName();
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addWebsiteFilter($websiteIds);
        $collection->addCategoriesFilter(['in' => $categoryId]);

        return $collection->getAllIds();
    }

    /**
     * @param array $productIds
     * @return array
     */
    private function prepareDataToSave(array $productIds): array
    {
        $result = [];
        foreach ($productIds as $id) {
            $result[] = [
                'product_id' => $id,
                'website_id' => 3
            ];
        }

        return $result;
    }

    /**
     * @param array $dataToSave
     * @return void
     */
    private function saveData(array $dataToSave): void
    {
        foreach ($dataToSave as $data) {
            $this->resourceConnection->getConnection()->insertOnDuplicate(
                $this->resourceConnection->getConnection()->getTableName(self::DESTINATION_TABLE),
                $data,
            );
        }
    }
}
