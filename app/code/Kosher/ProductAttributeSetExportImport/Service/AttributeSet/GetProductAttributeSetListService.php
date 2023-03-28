<?php
declare(strict_types=1);

namespace Kosher\ProductAttributeSetExportImport\Service\AttributeSet;

use Exception;
use Magento\Catalog\Api\AttributeSetRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as ProductAttributeCollection;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\Collection;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory as GroupCollection;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Serialize\Serializer\Json;

class GetProductAttributeSetListService
{
    private array $attributeSetList = [];
    private array $groupListData = [];
    private array $attributeList = [];
    private array $dataToCsv = [];
    private array $attributeSetNames = [];
    /**
     * @var AttributeSetRepositoryInterface
     */
    private AttributeSetRepositoryInterface $attributeSetRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var GroupCollection
     */
    private GroupCollection $groupCollection;

    /**
     * @var ProductAttributeCollection
     */
    private ProductAttributeCollection $productAttributeCollection;

    /**
     * @var Json
     */
    private Json $json;

    /**
     * @param AttributeSetRepositoryInterface $attributeSetRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param GroupCollection $groupCollection
     * @param ProductAttributeCollection $productAttributeCollection
     * @param Json $json
     */
    public function __construct(
        AttributeSetRepositoryInterface $attributeSetRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        GroupCollection $groupCollection,
        ProductAttributeCollection $productAttributeCollection,
        Json $json
    ) {
        $this->attributeSetRepository = $attributeSetRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->groupCollection = $groupCollection;
        $this->productAttributeCollection = $productAttributeCollection;
        $this->json = $json;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function execute(): array
    {
        $this->listAttributeSet();
        $this->groupListData = $this->getAttributeGroup(array_keys($this->attributeSetList))->getItems();
        foreach ($this->groupListData as $key => $items) {
            $productAttributeList = $this->getProductAttributeCollection($key);
            foreach ($productAttributeList as $attributeId => $productAttribute) {
                $this->attributeList[$attributeId] = $productAttribute;
                $attributeCode = $productAttribute->getAttributeCode();
                $this->dataToCsv[$attributeCode] = $productAttribute->getData();
                $attributeSetId = $productAttribute->getData('attribute_set_id');
                $attributeSetName = $this->attributeSetList[$attributeSetId]->getAttributeSetName();
                $this->attributeSetNames[$attributeCode][] = $attributeSetName;
                $this->dataToCsv[$attributeCode]['attribute_group_name'] = $items->getAttributeGroupName();
                $this->dataToCsv[$attributeCode]['attribute_group_code'] = $items->getAttributeGroupCode();
                unset($this->dataToCsv[$attributeCode]['attribute_set_id']);
                unset($this->dataToCsv[$attributeCode]['entity_attribute_id']);
                unset($this->dataToCsv[$attributeCode]['attribute_id']);
                unset($this->dataToCsv[$attributeCode]['attribute_group_id']);
                try {
                    $attributeOptionsValue = $productAttribute->getSource()->getAllOptions();
                    $this->convertAttributeOptionsToJson($attributeOptionsValue, $attributeCode);
                } catch (Exception $e) {
                }
            }
        }

        foreach ($this->attributeSetNames as $code => $data) {
            $this->dataToCsv[$code]['attribute_set_name'] = $this->convertListAttributeSetToJson($data);
        }

        $this->dataToCsv['header'] = array_keys(reset($this->dataToCsv));

        return array_reverse($this->dataToCsv);
    }

    /**
     * @param array $data
     * @return string
     */
    private function convertListAttributeSetToJson(array $data): string
    {
        return $this->json->serialize($data);
    }

    /**
     * @param array $options
     * @param string $attributeCode
     * @return void
     */
    private function convertAttributeOptionsToJson(array $options, string $attributeCode): void
    {
        $this->dataToCsv[$attributeCode]['attribute_options'] = $this->json->serialize($options);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function listAttributeSet(): void
    {
        $attributeSetList = null;
        try {
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $attributeSet = $this->attributeSetRepository->getList($searchCriteria);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }

        if ($attributeSet->getTotalCount()) {
            $attributeSetList = $attributeSet;
        }

        $this->attributeSetList = $attributeSetList->getItems();
        unset($this->attributeSetList[4]);
    }

    /**
     * @param array $attributeSetIds
     * @return Collection
     */
    private function getAttributeGroup(array $attributeSetIds): Collection
    {
        return  $this->groupCollection->create()
            ->addFieldToFilter('attribute_group_name', 'General')
            ->addFieldToFilter('attribute_set_id', ['in' => $attributeSetIds])
            ->load();
    }

    /**
     * @param int $groupId
     * @return array
     */
    private function getProductAttributeCollection(int $groupId): array
    {
        $groupAttributesCollection = $this->productAttributeCollection->create()
            ->setAttributeGroupFilter($groupId)
            ->load();

        return $groupAttributesCollection->getItems();
    }
}
