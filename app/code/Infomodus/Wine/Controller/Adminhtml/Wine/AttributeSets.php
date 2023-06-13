<?php

namespace Infomodus\Wine\Controller\Adminhtml\Wine;

use Infomodus\Wine\Controller\Adminhtml\Wine;
use Magento\Eav\Model\Attribute;
use Magento\Eav\Model\AttributeFactory;
use Magento\Eav\Model\AttributeRepository;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\Filesystem\DirectoryList;

class AttributeSets extends Wine
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
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $collectionSet
    )
    {
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
        $columns = $this->getColumnHeader();
        foreach ($columns as $column) {
            $header[] = $column;
        }
        /* Write Header */
        $stream->writeCsv($header);

        $attributes = $this->collectionSet->create();
        if ($attributes->count() > 0) {
            $products = [];
            /**
             * @var Attribute $attribute
             */
            foreach ($attributes as $attribute) {
                if($attribute->getAttributeSetName() != "Default") {
                    $products[] = array($attribute->getAttributeSetName(), json_encode($attribute->getData()));
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

        $csvfilename = 'AttributeSetsMerged.csv';
        return $this->_fileFactory->create($csvfilename, $content, DirectoryList::VAR_DIR);
    }

    public function getColumnHeader()
    {
        return ['Code', 'SerData'];
    }
}
