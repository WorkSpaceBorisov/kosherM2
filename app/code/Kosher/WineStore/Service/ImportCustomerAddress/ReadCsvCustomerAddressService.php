<?php
declare(strict_types=1);

namespace Kosher\WineStore\Service\ImportCustomerAddress;

use Exception;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\File\Csv;

class ReadCsvCustomerAddressService
{
    private const TARGET_TABLE = "customer_address_entity";
    private const TARGET_COLUMN = "entity_id";
    /**
     * @var Csv
     */
    private Csv $csv;

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @param Csv $csv
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Csv $csv,
        ResourceConnection $resourceConnection
    ) {
        $this->csv = $csv;
        $this->resourceConnection = $resourceConnection;
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
                $dataKey = $data[1];
                $res = array_combine($result['header'], $data);
                $result[$dataKey] = $res;
            }
        }

        $lastRecordCustomerAddress = $this->getLastRecordId();
        $i = 1;
        foreach ($result as $key => $data) {
            if ($key != 'header') {
                $result[$key]['_entity_id'] = $lastRecordCustomerAddress + $i;
                $result[$key]['_website'] = 'ariskosherwine';
            }

            $i++;
        }

        return $result;
    }

    private function getLastRecordId(): int
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select();

        $select->from(self::TARGET_TABLE, self::TARGET_COLUMN)->order(self::TARGET_COLUMN . ' desc');
        $entity = array_first($connection->fetchAll($select));

        return (int)$entity[self::TARGET_COLUMN];
    }
}
