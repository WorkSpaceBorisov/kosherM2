<?php
declare(strict_types=1);

namespace Kosher\WineStore\Service;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\ResourceConnection;

class CheckProductsFormCsvInDbService
{
    private const TARGET_TABLE_NAME = 'store_website';
    private array $arrayToSave = [];
    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ResourceConnection $resourceConnection
    ) {
        $this->productRepository = $productRepository;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param array $arrayData
     * @return void
     */
    public function execute(array $arrayData): void
    {
        ini_set('max_execution_time', '300');
        foreach ($arrayData as $sku => $productData) {
            if ($sku != 'header') {
                try {
                    $this->productRepository->deleteById($sku);
                } catch (Exception $exception) {
                }
            }
        }
    }

    /**
     * @param array $arrayData
     * @return array
     */
    public function deleteExistSkus(array $arrayData): array
    {
        $this->arrayToSave = $arrayData;
        $webSiteId = (int)$this->getWebSiteId();
        foreach ($arrayData as $sku => $productData) {
            if ($sku != 'header') {
                $this->arrayToSave[$sku]['url_key'] = $productData['url_key'] . '-' . rand(0, 9999);
                $this->arrayToSave[$sku]['store_view_code'] = 'ariskosherwine_store';
                $this->arrayToSave[$sku]['website_id'] = $webSiteId;
                $this->arrayToSave[$sku]['product_websites'] = 'ariskosherwine';
                if ($productData['product_online'] != 1 && $productData['product_online'] != 2) {
                    $this->arrayToSave[$sku]['product_online'] = 2;
                }

                if (empty($this->arrayToSave[$sku]['price'])) {
                    unset($this->arrayToSave[$sku]);
                }
            }
        }

        return $this->arrayToSave;
    }

    /**
     * @return void
     */
    public function deleteProductUrlKey(): void
    {
        $this->resourceConnection->getConnection()
            ->delete(
                $this->resourceConnection->getTableName('url_rewrite'),
                ['entity_type = ?' => 'product']
            );

        $this->resourceConnection->getConnection()
            ->delete(
                $this->resourceConnection->getTableName('url_rewrite'),
                ['entity_type = ?' => 'category']
            );
    }

    /**
     * @return string
     */
    private function getWebSiteId(): string
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select();

        $select->from(self::TARGET_TABLE_NAME)->where('code' . ' = ?', 'ariskosherwine');

        return $connection->fetchOne($select);
    }
}
