<?php
declare(strict_types=1);

namespace Kosher\WineStore\Model\Import;

use Kosher\WineStore\Query\GetWineStoreIdQuery;
use Kosher\WineStore\Query\GetWineWebSiteIdQuery;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Indexer\Processor;
use Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory;
use Magento\CustomerImportExport\Model\ResourceModel\Import\Customer\StorageFactory;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Stdlib\StringUtils;
use Magento\ImportExport\Model\Export\Factory;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ImportFactory;
use Magento\ImportExport\Model\ResourceModel\Helper;
use Magento\Store\Model\StoreManagerInterface;

class Customer extends \Magento\CustomerImportExport\Model\Import\Customer
{
    /**
     * @var GetWineWebSiteIdQuery
     */
    private GetWineWebSiteIdQuery $getWineWebSiteIdQuery;

    /**
     * @var GetWineStoreIdQuery
     */
    private GetWineStoreIdQuery $getWineStoreIdQuery;

    /**
     * @param GetWineWebSiteIdQuery $getWineWebSiteIdQuery
     * @param GetWineStoreIdQuery $getWineStoreIdQuery
     * @param StringUtils $string
     * @param ScopeConfigInterface $scopeConfig
     * @param ImportFactory $importFactory
     * @param Helper $resourceHelper
     * @param ResourceConnection $resource
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param StoreManagerInterface $storeManager
     * @param Factory $collectionFactory
     * @param Config $eavConfig
     * @param StorageFactory $storageFactory
     * @param CollectionFactory $attrCollectionFactory
     * @param CustomerFactory $customerFactory
     * @param array $data
     * @param Processor|null $indexerProcessor
     */
    public function __construct(
        GetWineWebSiteIdQuery $getWineWebSiteIdQuery,
        GetWineStoreIdQuery $getWineStoreIdQuery,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\ImportExport\Model\ImportFactory $importFactory,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\App\ResourceConnection $resource,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\ImportExport\Model\Export\Factory $collectionFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\CustomerImportExport\Model\ResourceModel\Import\Customer\StorageFactory $storageFactory,
        \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory $attrCollectionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        array $data = [],
        ?Processor $indexerProcessor = null
    ) {
        parent::__construct(
            $string,
            $scopeConfig,
            $importFactory,
            $resourceHelper,
            $resource,
            $errorAggregator,
            $storeManager,
            $collectionFactory,
            $eavConfig,
            $storageFactory,
            $attrCollectionFactory,
            $customerFactory,
            $data,
            $indexerProcessor
        );
        $this->getWineWebSiteIdQuery = $getWineWebSiteIdQuery;
        $this->getWineStoreIdQuery = $getWineStoreIdQuery;
    }

    /**
     * @param array $entitiesToCreate
     * @param array $entitiesToUpdate
     * @return Customer
     */
    protected function _saveCustomerEntities(array $entitiesToCreate, array $entitiesToUpdate): Customer
    {
        $this->customerFields[] = 'website_id';
        $wineWebSiteId = (int)$this->getWineWebSiteIdQuery->execute();
        $wineStoreId = (int)$this->getWineStoreIdQuery->execute();
        $entitiesToCreates = $this->addIdsToCustomerData($entitiesToCreate, $wineWebSiteId, $wineStoreId);
        $entitiesToUpdates = $this->addIdsToCustomerData($entitiesToUpdate, $wineWebSiteId, $wineStoreId);

        return parent::_saveCustomerEntities($entitiesToCreates, $entitiesToUpdates);
    }

    /**
     * @param array $customerData
     * @param int $webSiteId
     * @param int $storeId
     * @return array
     */
    private function addIdsToCustomerData(array $customerData, int $webSiteId, int $storeId): array
    {
        $result = [];
        foreach ($customerData as $datum) {
            $datum['website_id'] = $webSiteId;
            $datum['store_id'] = $storeId;
            $result[] = $datum;
        }

        return $result;
    }
}
