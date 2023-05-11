<?php

namespace Infomodus\Wine\Controller\Adminhtml\Wine;

use Infomodus\Wine\Controller\Adminhtml\Wine;
use Magento\Eav\Model\Attribute;
use Magento\Eav\Model\AttributeFactory;
use Magento\Eav\Model\AttributeRepository;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\Filesystem\DirectoryList;

class Attributes extends Wine
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Infomodus_Wine::wine_attributes_view';
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    private $_fileFactory;
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $directory;
    /**
     * @var AttributeRepository
     */
    private $attributeRepository;
    /**
     * @var AttributeFactory
     */
    private $attribute;
    /**
     * @var CollectionFactory
     */
    private $collectionAttribute;
    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;
    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    private $directoryRead;
    /**
     * @var EavSetup
     */
    private $eavSetup;
    /**
     * @var \Magento\Eav\Model\ResourceModel\Form\Attribute\Collection
     */
    private $collectionFormAttr;
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory
     */
    private $collectionSet;
    /**
     * @var CollectionFactory
     */
    private $collectionEntity;
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem $filesystem,
        AttributeRepository $attributeRepository,
        AttributeFactory $attribute,
        CollectionFactory $collectionAttribute,
        \Magento\Eav\Model\Config $eavConfig,
        EavSetup $eavSetup,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $collectionFormAttr,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $collectionSet,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $collectionEntity,
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->_fileFactory = $fileFactory;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->directoryRead = $filesystem->getDirectoryRead(DirectoryList::VAR_DIR);
        parent::__construct($context);
        $this->attributeRepository = $attributeRepository;
        $this->attribute = $attribute;
        $this->collectionAttribute = $collectionAttribute;
        $this->eavConfig = $eavConfig;
        $this->eavSetup = $eavSetup;
        $this->collectionFormAttr = $collectionFormAttr;
        $this->collectionSet = $collectionSet;
        $this->collectionEntity = $collectionEntity;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $name = date('m_d_Y_H_i_s');
        $filepath = 'export/custom' . $name . '.csv';
        $this->directory->create('export');
        /* Open file */
        $stream = $this->directory->openFile($filepath, 'w+');
        $stream->lock();
        $fileRead = $this->directoryRead->openFile($filepath);
        $i = 0;
        $rows = [];
        while (!$fileRead->eof()) {
            $row = $fileRead->readCsv();
            if (empty($row) || ($row[0] == "Code" && $row[1] == "SerData")) {
                continue;
            }
            $rows[] = $row[0];
            $i++;
            if ($i > 100000) {
                break;
            }
        }
        $fileRead->close();
        $columns = $this->getColumnHeader();
        foreach ($columns as $column) {
            $header[] = $column;
        }
        /* Write Header */
        $stream->writeCsv($header);

        /**
         * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection $attributes
         */
        $attributes = $this->collectionAttribute->create();
        if ($attributes->count() > 0) {
            $products = [];
            /**
             * @var Attribute $attribute
             */
            foreach ($attributes as $attribute) {
                if ($attribute->getEntityTypeId() == 4) {
                    $attributeSetup = $this->eavSetup->getAttribute(4, $attribute->getAttributeCode());
                    //print_r($attributeSetup);

                    $attribute1 = $this->eavConfig->getAttribute('catalog_product', $attribute->getAttributeCode());
                    $options = $attribute1->getSource()->getAllOptions();

                    $bind = ['attribute_id' => $attribute->getAttributeId()];
                    $table = $this->moduleDataSetup->getTable('eav_entity_attribute');
                    $select = $this->moduleDataSetup->getConnection()->select()->from(
                        $table
                    )->joinLeft(
                        ['eav_attribute_group' => $attributes->getTable('eav_attribute_group')],
                        $table . '.attribute_group_id = eav_attribute_group.attribute_group_id',
                        ["eav_attribute_group.attribute_group_code", "eav_attribute_group.attribute_set_id", "eav_attribute_group.attribute_group_name"]
                    )->where(
                        $table . '.attribute_id = :attribute_id'
                    );
                    $result = $this->moduleDataSetup->getConnection()->fetchAll($select, $bind);
                    $setsIdArray = [];
                    $groupsIdArray = [];
                    if ($result) {
                        foreach ($result as $item) {
                            $setsIdArray[] = $item['attribute_set_id'];
                            $groupsIdArray[] = ["group" => $item['attribute_group_code'], "group_name" => $item['attribute_group_name'], "set" => $item['attribute_set_id']];
                        }
                    }
                    if (in_array($attribute->getAttributeCode(), $rows)) {
                        $products[] = [$attribute->getAttributeCode(), json_encode($attributeSetup), json_encode($options), json_encode(array_unique($setsIdArray)), json_encode($groupsIdArray)];
                    } else {
                        $products[] = [$attribute->getAttributeCode(), "", "", json_encode(array_unique($setsIdArray)), json_encode($groupsIdArray)];
                    }
                }
            }
            foreach ($products as $item) {
                $stream->writeCsv($item);
            }
        }
        $stream->close();

        $content = [];
        $content['type'] = 'filename'; // must keep filename
        $content['value'] = $filepath;
        $content['rm'] = '1'; //remove csv from var folder

        $csvfilename = 'AttributesMerged.csv';
        return $this->_fileFactory->create($csvfilename, $content, DirectoryList::VAR_DIR);
    }

    public function getColumnHeader()
    {
        return ['Code', 'SerData', "Options", "Sets", "Groups"];
    }
}
