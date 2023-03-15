<?php
declare(strict_types=1);

namespace Kosher\ConvertProductCsvM1File\Controller\Adminhtml\Convert;

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
use Magento\Theme\Model\Design\Config\FileUploader\FileProcessor;

class Upload extends Action
{
    const ADMIN_RESOURCE = 'Kosher_ConvertProductCsvM1File::convert';

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
     * @param Context $context
     * @param FileProcessor $fileProcessor
     * @param Filesystem $fileSystem
     * @param UploaderFactory $uploaderFactory
     */
    public function __construct(
        Action\Context $context,
        FileProcessor $fileProcessor,
        Filesystem $fileSystem,
        UploaderFactory $uploaderFactory
    ) {
        parent::__construct($context);
        $this->fileProcessor = $fileProcessor;
        $this->fileSystem = $fileSystem;
        $this->uploaderFactory = $uploaderFactory;
    }

    /**
     * @return ResponseInterface|Json|(Json&ResultInterface)|ResultInterface
     * @throws FileSystemException
     */
    public function execute()
    {
        $destinationPath = $this->getDestinationPath();
        try {
            $uploader = $this->uploaderFactory
                ->create(['fileId' => $this->fileId])
                ->setAllowCreateFolders(true)
                ->setAllowRenameFiles(true)
                ->setAllowedExtensions($this->allowedExtensions);
            $result = $uploader->save($destinationPath);
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
            ->getDirectoryWrite(DirectoryList::TMP)
            ->getAbsolutePath('/');
    }
}
