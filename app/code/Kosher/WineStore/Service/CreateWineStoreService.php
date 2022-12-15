<?php
declare(strict_types=1);

namespace Kosher\WineStore\Service;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\GroupFactory;
use Magento\Store\Model\ResourceModel\Group;
use Magento\Store\Model\ResourceModel\Store;
use Magento\Store\Model\ResourceModel\Website;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\WebsiteFactory;

class CreateWineStoreService
{
    /**
     * @var Group
     */
    private Group $groupResourceModel;

    /**
     * @var GroupFactory
     */
    private GroupFactory $groupFactory;

    /**
     * @var Store
     */
    private Store $storeResourceModel;

    /**
     * @var StoreFactory
     */
    private StoreFactory $storeFactory;

    /**
     * @var Website
     */
    private Website $websiteResourceModel;

    /**
     * @var WebsiteFactory
     */
    private WebsiteFactory $websiteFactory;

    /**
     * @param Group $groupResourceModel
     * @param GroupFactory $groupFactory
     * @param Store $storeResourceModel
     * @param StoreFactory $storeFactory
     * @param Website $websiteResourceModel
     * @param WebsiteFactory $websiteFactory
     */
    public function __construct(
        Group $groupResourceModel,
        GroupFactory $groupFactory,
        Store $storeResourceModel,
        StoreFactory $storeFactory,
        Website $websiteResourceModel,
        WebsiteFactory $websiteFactory
    ) {
        $this->groupResourceModel = $groupResourceModel;
        $this->groupFactory = $groupFactory;
        $this->storeResourceModel = $storeResourceModel;
        $this->storeFactory = $storeFactory;
        $this->websiteResourceModel = $websiteResourceModel;
        $this->websiteFactory = $websiteFactory;
    }

    /**
     * @return true|void
     * @throws AlreadyExistsException
     * @throws LocalizedException
     */
    public function execute()
    {
        $attribute = [
            'website_code' => 'ariskosherwine',
            'website_name' => 'Ariskosherwine Website',
            'group_name' => 'Ariskosherwine Store',
            'store_code' => 'ariskosherwine_store',
            'store_name' => 'Ariskosherwine Store',
            'is_active' => '1'
        ];

        $store = $this->storeFactory->create();
        $store->load($attribute['store_code']);

        if (!$store->getId()) {
            /** @var \Magento\Store\Model\Website $website */
            $website = $this->websiteFactory->create();
            $website->load($attribute['website_code']);
            $website = $this->setWebID($website, $attribute);

            /** @var \Magento\Store\Model\Group $group */
            $group = $this->groupFactory->create();
            $group->setWebsiteId($website->getWebsiteId());
            $group->setName($attribute['group_name']);
            $group->setCode($attribute['store_code']);
            $this->groupResourceModel->save($group);

            $group = $this->groupFactory->create();
            $group->load($attribute['group_name'], 'name');
            $store->setCode($attribute['store_code']);
            $store->setName($attribute['store_name']);
            $store->setWebsite($website);
            $store->setGroupId($group->getId());
            $store->setData('is_active', $attribute['is_active']);
            $this->storeResourceModel->save($store);
            return true;
        }
    }

    /**
     * @param $website
     * @param $attribute
     * @return mixed
     * @throws AlreadyExistsException
     */
    public function setWebID($website, $attribute)
    {
        if (!$website->getId()) {
            $website->setCode($attribute['website_code']);
            $website->setName($attribute['website_name']);
            $this->websiteResourceModel->save($website);
        }
        return $website;
    }
}
