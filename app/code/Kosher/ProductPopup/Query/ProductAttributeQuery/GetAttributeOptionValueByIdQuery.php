<?php
declare(strict_types=1);

namespace Kosher\ProductPopup\Query\ProductAttributeQuery;

use Magento\Framework\App\ResourceConnection;

class GetAttributeOptionValueByIdQuery
{
    private const EAV_ATTRIBUTE_OPTION_VALUE = 'eav_attribute_option_value';
    private const EAV_ATTRIBUTE_OPTION_VALUE_OPTION_ID_FIELD = 'option_id';
    private const EAV_ATTRIBUTE_OPTION_VALUE_STORE_ID_FIELD = 'store_id';

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $connection;

    /**
     * @param ResourceConnection $connection
     */
    public function __construct(
        ResourceConnection $connection
    ) {
        $this->connection = $connection;
    }

    /**
     * @param int $optionId
     * @param int $storeId
     * @return string|null
     */
    public function execute(int $optionId, int $storeId): string|null
    {
        $connection = $this->connection->getConnection();
        $select = $connection->select();

        $select->from(self::EAV_ATTRIBUTE_OPTION_VALUE, 'value')
            ->where(self::EAV_ATTRIBUTE_OPTION_VALUE_OPTION_ID_FIELD .' = ?', $optionId)
            ->where(self::EAV_ATTRIBUTE_OPTION_VALUE_STORE_ID_FIELD .' = ?', $storeId);

        $result = $connection->fetchAll($select);

        if (empty($result)){
            return null;
        }

        return $result[0]['value'];
    }
}
