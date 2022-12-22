<?php
declare(strict_types=1);

namespace Kosher\StoresConfiguration\Setup\Patch\Data;

use Magento\Customer\Api\CustomerMetadataManagementInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

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
     */
    public function apply(): RemoveMailchimpAttributesDataPatch|static
    {
        $entityTypeCode = CustomerMetadataManagementInterface::ENTITY_TYPE_CUSTOMER;
        $attributeCode = 'mailchimp_sync_delta';
        try {
            $attributeData = $this->attributeRepository->get($entityTypeCode, $attributeCode);
            $this->attributeRepository->delete($attributeData);
        } catch (\Exception $exception) {
        }

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
