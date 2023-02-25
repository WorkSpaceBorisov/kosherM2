<?php
declare(strict_types=1);

namespace Kosher\ProductAttributeSetExportImport\Service\AttributeSet;

use Magento\Eav\Api\AttributeGroupRepositoryInterface;
use Magento\Eav\Api\Data\AttributeGroupInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

class AssignAttributeSetToGroupService
{
    /**
     * @var AttributeGroupInterfaceFactory
     */
    private AttributeGroupInterfaceFactory $attributeGroupInterfaceFactory;

    /**
     * @var AttributeGroupRepositoryInterface
     */
    private AttributeGroupRepositoryInterface $attributeGroupRepository;

    /**
     * @param AttributeGroupInterfaceFactory $attributeGroupInterfaceFactory
     * @param AttributeGroupRepositoryInterface $attributeGroupRepository
     */
    public function __construct(
        AttributeGroupInterfaceFactory $attributeGroupInterfaceFactory,
        AttributeGroupRepositoryInterface $attributeGroupRepository
    ) {
        $this->attributeGroupInterfaceFactory = $attributeGroupInterfaceFactory;
        $this->attributeGroupRepository = $attributeGroupRepository;
    }

    /**
     * @param string $attributeGroupName
     * @param int $attributeSetId
     * @return int
     * @throws NoSuchEntityException
     * @throws StateException
     */
    public function execute(string $attributeGroupName, int $attributeSetId): int
    {
        $attributeGroup = $this->attributeGroupInterfaceFactory->create();

        $attributeGroup->setAttributeSetId($attributeSetId);
        $attributeGroup->setAttributeGroupName($attributeGroupName);
        return (int)$this->attributeGroupRepository->save($attributeGroup)->getAttributeGroupId();
    }
}
