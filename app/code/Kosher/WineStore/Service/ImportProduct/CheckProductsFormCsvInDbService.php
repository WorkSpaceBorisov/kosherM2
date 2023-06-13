<?php
declare(strict_types=1);

namespace Kosher\WineStore\Service\ImportProduct;

use Kosher\WineStore\Query\GetWineWebSiteIdQuery;
use Magento\Framework\App\ResourceConnection;

class CheckProductsFormCsvInDbService
{
    private array $arrayToSave = [];

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @var GetWineWebSiteIdQuery
     */
    private GetWineWebSiteIdQuery $getWineWebSiteIdQuery;

    /**
     * @param ResourceConnection $resourceConnection
     * @param GetWineWebSiteIdQuery $getWineWebSiteIdQuery
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        GetWineWebSiteIdQuery $getWineWebSiteIdQuery
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->getWineWebSiteIdQuery = $getWineWebSiteIdQuery;
    }

    /**
     * @param array $arrayData
     * @return array
     */
    public function deleteExistSkus(array $arrayData): array
    {
        $this->arrayToSave = $arrayData;
        $webSiteId = (int)$this->getWineWebSiteIdQuery->execute();
        $i = 1;
        foreach ($arrayData as $sku => $productData) {
            if ($sku != 'header') {
                $this->arrayToSave[$sku]['url_key'] = $productData['url_key'] . '-' . $i;
                $this->arrayToSave[$sku]['store_view_code'] = 'ariskosherwine_store';
                $this->arrayToSave[$sku]['website_id'] = $webSiteId;
                $this->arrayToSave[$sku]['product_websites'] = 'ariskosherwine';
                if ($productData['product_online'] != 1 && $productData['product_online'] != 2) {
                    $this->arrayToSave[$sku]['product_online'] = 2;
                }

                if (empty($this->arrayToSave[$sku]['name'])) {
                    unset($this->arrayToSave[$sku]);
                }
            }

            $i++;
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
}
