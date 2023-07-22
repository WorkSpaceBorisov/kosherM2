<?php
declare(strict_types=1);

namespace Kosher\StoresConfiguration\Model\ResourceModel\Address\Attribute\Source;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Config\Share as CustomerShareConfig;
use Magento\Directory\Model\AllowedCountries;
use Magento\Directory\Model\ResourceModel\Country\Collection as CountryCollection;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory as OptionCollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory as AttrubuteOptionFactory;
use Magento\Framework\App\Request\Http;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class CountryWithWebsites extends \Magento\Customer\Model\ResourceModel\Address\Attribute\Source\CountryWithWebsites
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var AllowedCountries
     */
    private AllowedCountries $allowedCountriesReader;

    /**
     * @var CustomerShareConfig
     */
    private CustomerShareConfig $shareConfig;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var Http
     */
    private Http $request;

    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;

    /**
     * @var CountryCollectionFactory
     */
    private CountryCollectionFactory $countriesFactory;

    /**
     * @param OptionCollectionFactory $attrOptionCollectionFactory
     * @param AttrubuteOptionFactory $attrOptionFactory
     * @param CountryCollectionFactory $countriesFactory
     * @param AllowedCountries $allowedCountriesReader
     * @param StoreManagerInterface $storeManager
     * @param CustomerShareConfig $shareConfig
     * @param Http $request
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        OptionCollectionFactory $attrOptionCollectionFactory,
        AttrubuteOptionFactory $attrOptionFactory,
        CountryCollectionFactory $countriesFactory,
        AllowedCountries $allowedCountriesReader,
        StoreManagerInterface $storeManager,
        CustomerShareConfig $shareConfig,
        Http $request,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct(
            $attrOptionCollectionFactory,
            $attrOptionFactory,
            $countriesFactory,
            $allowedCountriesReader,
            $storeManager,
            $shareConfig,
            $request,
            $customerRepository
        );
        $this->allowedCountriesReader = $allowedCountriesReader;
        $this->shareConfig = $shareConfig;
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->customerRepository = $customerRepository;
        $this->countriesFactory = $countriesFactory;
    }

    /**
     * @inheritdoc
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false): array
    {
        if (!$this->options) {
            $websiteIds = [];

            if (!$this->shareConfig->isGlobalScope()) {
                $allowedCountries = [];

                foreach ($this->storeManager->getWebsites() as $website) {
                    $countries = $this->allowedCountriesReader
                        ->getAllowedCountries(ScopeInterface::SCOPE_WEBSITE, $website->getId());
                    $allowedCountries[] = $countries;

                    foreach ($countries as $countryCode) {
                        $websiteIds[$countryCode][] = $website->getId();
                    }
                }

                $allowedCountries = array_unique(array_merge([], ...$allowedCountries));
            } else {
                // Address can be added only for the allowed country list.
                $websiteId = null;
                $customerId = $this->request->getParam('parent_id') ?? null;
                if ($customerId) {
                    $customer = $this->customerRepository->getById($customerId);
                    $websiteId = $customer->getWebsiteId();
                }

                $allowedCountries = $this->allowedCountriesReader->getAllowedCountries(
                    ScopeInterface::SCOPE_WEBSITE,
                    $websiteId
                );
            }

            $this->options = $this->createCountriesCollection()
                ->addFieldToFilter('country_id', ['in' => $allowedCountries])
                ->toOptionArray();

            foreach ($this->options as &$option) {
                if (isset($websiteIds[$option['value']])) {
                    $option['website_ids'] = $websiteIds[$option['value']];
                }
            }
        }

        return $this->options;
    }

    /**
     * Create Countries Collection with all countries
     *
     * @return CountryCollection
     */
    private function createCountriesCollection(): CountryCollection
    {
        return $this->countriesFactory->create();
    }
}
