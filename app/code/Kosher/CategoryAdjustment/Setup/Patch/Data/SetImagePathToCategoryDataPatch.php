<?php

namespace Kosher\CategoryAdjustment\Setup\Patch\Data;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class SetImagePathToCategoryDataPatch implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ResourceConnection $resourceConnection
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @return void
     */
    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $iconPath = [
            'Bakery' => '/media/catalog/category/Bakery.png',
            'Fresh' => '/media/catalog/category/Fresh.png',
            'Groceries' => '/media/catalog/category/Groceries.png',
            'Health' => '/media/catalog/category/Health.png',
            'Household' => '/media/catalog/category/Household.png',
            'Judaica' => '/media/catalog/category/Judaica.png',
            'Snacks' => '/media/catalog/category/Snacks.png',
            'Wine & Spirits' => '/media/catalog/category/Wine.png',
        ];
        $categoriesEntityId = $this->getCategoryEntityId();
        if ($categoriesEntityId) {
            $categoryIconAttributeId = $this->getCategoryIconAttributeId();
            foreach ($categoriesEntityId as $categoryEntityId)
            {

                $iconData = [
                    'attribute_id' => $categoryIconAttributeId,
                    'store_id' => 0,
                    'entity_id' => $categoryEntityId['entity_id'],
                    'value' => $iconPath[trim($categoryEntityId['value'])],
                ];

                $this->setCategoryIconPath($iconData);
            }
        }


        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @return int
     */
    private function getCategoryIconAttributeId(): int
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select();

        $select->from('eav_attribute', 'attribute_id')
            ->where('attribute_code = ?', 'category_icon');

        return (int)$connection->fetchOne($select);
    }

    /**
     * @return array|null
     */
    private function getCategoryEntityId(): ?array
    {
        $categoriesTitle = [
            'Bakery',
            'Fresh',
            'Groceries',
            'Health',
            'Household',
            'Judaica',
            'Snacks',
            'Wine & Spirits',
        ];
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select();

        $select->from('catalog_category_entity_varchar', ['entity_id','value'])
            ->where('value IN(?)', [$categoriesTitle])->group('entity_id');

        $result = $connection->fetchAll($select);

        if (empty($result)) {
            return null;
        }

        return $result;
    }

    /**
     * @param array $data
     * @return void
     */
    private function setCategoryIconPath(array $data): void
    {
        $connection = $this->resourceConnection->getConnection();
        $connection->insertOnDuplicate('catalog_category_entity_varchar', $data, ['attribute_id','store_id', 'entity_id', 'value',]);
    }
}
