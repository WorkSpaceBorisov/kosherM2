<?php
declare(strict_types=1);

namespace Kosher\StoresConfiguration\Setup\Patch\Data;

use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Customer\Api\CustomerMetadataManagementInterface;
class RemoveMailchimpAttributesDataPatch implements DataPatchInterface
{
    /**
     * @var AttributeRepositoryInterface
     */
    private AttributeRepositoryInterface $attributeRepository;

    /**
     * @param AttributeRepositoryInterface $attributeRepository
     */
    public function __construct(
        AttributeRepositoryInterface $attributeRepository
    ) {
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * @return RemoveMailchimpAttributesDataPatch|$this
     * @throws NoSuchEntityException
     * @throws StateException
     */
    public function apply(): RemoveMailchimpAttributesDataPatch|static
    {
        $entityTypeCode = CustomerMetadataManagementInterface::ENTITY_TYPE_CUSTOMER;
        $attributeCode = 'mailchimp_sync_delta';
        $attributeData = $this->attributeRepository->get($entityTypeCode, $attributeCode);
        $this->attributeRepository->delete($attributeData);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
