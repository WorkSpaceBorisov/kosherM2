<?php
declare(strict_types=1);

namespace Kosher\WineStore\Query;

use Magento\Framework\App\ResourceConnection;

class GetWineStoreIdQuery
{
    private const TARGET_TABLE_NAME = 'store';
    private const CHECK_FIELD = 'code';
    private const CHECK_STORE_CODE = 'ariskosherwine_store';

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @return string
     */
    public function execute(): string
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select();

        $select->from(self::TARGET_TABLE_NAME)->where(self::CHECK_FIELD . ' = ?', self::CHECK_STORE_CODE);

        return $connection->fetchOne($select);
    }
}
