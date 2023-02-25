<?php
declare(strict_types=1);

namespace Kosher\ProductAttributeSetExportImport\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Theme\Model\Design\Config\FileUploader\FileProcessor;

class Upload extends Action
{
    const ADMIN_RESOURCE = 'Kosher_ProductAttributeSetExportImport::import';

    private $allowedExtensions = ['csv'];
    private $fileId = 'file_name';
    /**
     * @var FileProcessor
     */
    private FileProcessor $fileProcessor;

    /**
     * @var Filesystem
     */
    private Filesystem $fileSystem;

    /**
     * @var UploaderFactory
     */
    private UploaderFactory $uploaderFactory;

    /**
     * @var SessionManagerInterface
     */
    private SessionManagerInterface $session;

    /**
     * @param Context $context
     * @param FileProcessor $fileProcessor
     * @param Filesystem $fileSystem
     * @param UploaderFactory $uploaderFactory
     * @param SessionManagerInterface $session
     */
    public function __construct(
        Action\Context $context,
        FileProcessor $fileProcessor,
        Filesystem $fileSystem,
        UploaderFactory $uploaderFactory,
        SessionManagerInterface $session
    ) {
        parent::__construct($context);
        $this->fileProcessor = $fileProcessor;
        $this->fileSystem = $fileSystem;
        $this->uploaderFactory = $uploaderFactory;
        $this->session = $session;
    }

    /**
     * @return ResponseInterface|Json|(Json&ResultInterface)|ResultInterface
     * @throws FileSystemException
     */
    public function execute()
    {
        $destinationPath = $this->getDestinationPath() . 'importexport/';
        try {
            $uploader = $this->uploaderFactory
                ->create(['fileId' => $this->fileId])
                ->setAllowCreateFolders(true)
                ->setAllowRenameFiles(true)
                ->setAllowedExtensions($this->allowedExtensions);
            $result = $uploader->save($destinationPath);
            $filePath = $result['path'] . $result['file'];
            $this->session->setAttributeCsvFilePath($filePath);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }

    /**
     * @return string
     * @throws FileSystemException
     */
    public function getDestinationPath(): string
    {
        return $this->fileSystem
            ->getDirectoryWrite(DirectoryList::VAR_DIR)
            ->getAbsolutePath('/');
    }
}
