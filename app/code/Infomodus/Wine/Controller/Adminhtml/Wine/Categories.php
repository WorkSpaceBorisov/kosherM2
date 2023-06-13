<?php

namespace Infomodus\Wine\Controller\Adminhtml\Wine;

use Infomodus\Wine\Controller\Adminhtml\Wine;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Eav\Model\Attribute;
use Magento\Eav\Model\AttributeFactory;
use Magento\Eav\Model\AttributeRepository;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\Filesystem\DirectoryList;

class Categories extends Wine
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
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $categoryCollectionFactory;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

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
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        CategoryRepository $categoryRepository
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
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryRepository = $categoryRepository;
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
        /**
         * @var \Magento\Catalog\Model\ResourceModel\Category\Collection $attributes
         */
        $attributes = $this->categoryCollectionFactory->create();
        $attributes->addAttributeToSelect("*");
        if ($attributes->count() > 0) {
            $products = [];
            /**
             * @var Attribute $attribute
             */
            foreach ($attributes as $attribute) {
                $category = $attribute->getData();
                if ($category['parent_id'] > 1) {
                    $parentCategory = $this->categoryRepository->get($category['parent_id']);
                    $defCatUrl = $parentCategory->getCustomAttribute('url_key')->getValue();
                    if ($defCatUrl == 'default-category') {
                        $defCatUrl = "wine-root-category";
                    }

                    $category['parent_name'] = $parentCategory->getName();
                    $category['parent_url'] = $defCatUrl;
                    //print_r($category);
                    $products[] = array($attribute->getUrlKey(), json_encode($category));
                }
            }

            foreach ($products as $item) {
                $stream->writeCsv($item);
            }
        }
        $stream->close();
        //exit;

        $content = [];
        $content['type'] = 'filename'; // must keep filename
        $content['value'] = $filepath;
        $content['rm'] = '1'; //remove csv from var folder

        $csvfilename = 'Categories.csv';
        return $this->_fileFactory->create($csvfilename, $content, DirectoryList::VAR_DIR);
    }

    public function getColumnHeader()
    {
        return ['Code', 'SerData'];
    }
}
