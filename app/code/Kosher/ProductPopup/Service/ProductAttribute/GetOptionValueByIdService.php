<?php
declare(strict_types=1);

namespace Kosher\ProductPopup\Service\ProductAttribute;

use Kosher\ProductPopup\Query\ProductAttributeQuery\GetAttributeOptionValueByIdQuery;

class GetOptionValueByIdService
{
    /**
     * @var GetAttributeOptionValueByIdQuery
     */
    private GetAttributeOptionValueByIdQuery $getAttributeOptionValueByIdQuery;

    /**
     * @param GetAttributeOptionValueByIdQuery $getAttributeOptionValueByIdQuery
     */
    public function __construct(
        GetAttributeOptionValueByIdQuery $getAttributeOptionValueByIdQuery
    ) {
        $this->getAttributeOptionValueByIdQuery = $getAttributeOptionValueByIdQuery;
    }

    /**
     * @param array $attributeOptionId
     * @param int $storeId
     * @return array
     */
    public function execute(array $attributeOptionId, int $storeId): array
    {
        $result = [];
        $attributeOptionId = $this->changeStructureAttributeOptionData($attributeOptionId);
        foreach ($attributeOptionId as $attributeCode => $optionId) {
            if (!is_array($optionId)) {
                $result[$attributeCode] = [
                    $optionId => $this->getAttributeOptionValueByIdQuery->execute($optionId, $storeId),
                ];
            } else {
                $arrayResult = [];
                foreach ($optionId as $id) {
                    $arrayResult[$id] = $this->getAttributeOptionValueByIdQuery->execute($id, $storeId);
                }

                $result[$attributeCode] = $arrayResult;
            }
        }

        return $result;

    }

    /**
     * @param array $data
     * @return array
     */
    private function changeStructureAttributeOptionData(array $data): array
    {
        $result = [];
        foreach ($data as $key => $optionId) {
            if (!empty($optionId)) {
                $status = strpos($optionId, ',');
                if ($status !== false) {
                    $resIds = explode(',', $optionId);
                    $result[$key] = $resIds;
                } else {
                    $result[$key] = $optionId;
                }
            }
        }

        return $result;
    }
}
