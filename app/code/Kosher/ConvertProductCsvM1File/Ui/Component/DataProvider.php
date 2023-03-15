<?php
declare(strict_types=1);

namespace Kosher\ConvertProductCsvM1File\Ui\Component;

use Magento\Framework\Api\Filter;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    /**
     * @return array
     */
    public function getData(): array
    {
        return [];
    }

    /**
     * @param Filter $filter
     * @return null
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        return null;
    }
}
