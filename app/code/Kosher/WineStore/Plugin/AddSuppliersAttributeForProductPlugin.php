<?php
declare(strict_types=1);

namespace Kosher\WineStore\Plugin;

use Magento\CatalogImportExport\Model\Import\Product\Type\AbstractType;

class AddSuppliersAttributeForProductPlugin
{
    /**
     * @param AbstractType $subject
     * @param array $result
     * @param array $rowData
     * @param bool $withDefaultValue
     * @return array
     */
    public function afterPrepareAttributesWithDefaultValueForSave(AbstractType $subject, array $result, array $rowData, $withDefaultValue = true): array
    {
        $result['suppliers'] = 'kosher Wijn BVBA';
        return $result;
    }
}
