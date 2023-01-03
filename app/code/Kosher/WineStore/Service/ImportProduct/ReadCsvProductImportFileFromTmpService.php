<?php
declare(strict_types=1);

namespace Kosher\WineStore\Service\ImportProduct;

use Exception;
use Magento\Framework\File\Csv;

class ReadCsvProductImportFileFromTmpService
{
    /**
     * @var Csv
     */
    private Csv $csv;

    /**
     * @param Csv $csv
     */
    public function __construct(
        Csv $csv
    ) {
        $this->csv = $csv;
    }

    /**
     * @param string $filePath
     * @return array
     * @throws Exception
     */
    public function execute(string $filePath): array
    {
        $csvData = $this->csv->getData($filePath);
        $result = [];
        foreach ($csvData as $row => $data) {
            if ($row == 0) {
                $result['header'] = $data;
            } else {
                $dataKey = $data[0];
                $result[$dataKey]= array_combine($result['header'], $data);
            }
        }

        return $result;
    }
}
