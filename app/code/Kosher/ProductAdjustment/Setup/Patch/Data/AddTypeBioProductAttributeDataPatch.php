<?php
declare(strict_types=1);

namespace Kosher\ProductAdjustment\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Model\Product\Attribute\Frontend\Image as ImageFrontendModel;
use \Zend_Validate_Exception;
class AddTypeBioProductAttributeDataPatch implements DataPatchInterface
{

    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private EavSetupFactory $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory          $eavSetupFactory
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @return void
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     */
    public function apply(): void
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute(
            Product::ENTITY,
            'gluten_free',
            [
                'is_visible_in_grid' => true,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => true,
                'visible' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'label' => 'Gluten Free',
                'source' => null,
                'type' => 'varchar',
                'is_used_in_grid' => true,
                'required' => false,
                'input' => 'media_image',
                'frontend' => ImageFrontendModel::class,
                'used_in_product_listing' => true,
                'is_filterable_in_grid' => true,
                'sort_order' => 10,
            ]
        );
        $eavSetup->addAttribute(
            Product::ENTITY,
            'sugar_free',
            [
                'is_visible_in_grid' => true,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => true,
                'visible' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'label' => 'Sugar Free',
                'source' => null,
                'type' => 'varchar',
                'is_used_in_grid' => true,
                'required' => false,
                'input' => 'media_image',
                'frontend' => ImageFrontendModel::class,
                'used_in_product_listing' => true,
                'is_filterable_in_grid' => true,
                'sort_order' => 11,
            ]
        );
        $eavSetup->addAttribute(
            Product::ENTITY,
            'bio_attribute',
            [
                'is_visible_in_grid' => true,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => true,
                'visible' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'label' => 'BIO',
                'source' => null,
                'type' => 'varchar',
                'is_used_in_grid' => true,
                'required' => false,
                'input' => 'media_image',
                'frontend' => ImageFrontendModel::class,
                'used_in_product_listing' => true,
                'is_filterable_in_grid' => true,
                'sort_order' => 12,
            ]
        );
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases(): array
    {
        return [];
    }
}
