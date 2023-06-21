<?php

namespace Kosher\StoresConfiguration\Plugin;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\ImportExport\Controller\Adminhtml\Export\Export;
use Magento\ImportExport\Model\Export as ExportModel;
use Magento\ImportExport\Model\Export\Entity\ExportInfoFactory;
use Magento\ImportExport\Api\ExportManagementInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Filesystem\Driver\File;

class ExportProductPlugin
{
    /**
     * @var ExportInfoFactory
     */
    private ExportInfoFactory $exportInfoFactory;

    /**
     * @var ResultFactory
     */
    private ResultFactory $resultFactory;

    /**
     * @var ExportManagementInterface
     */
    private ExportManagementInterface $exportManagement;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @var DirectoryList
     */
    private DirectoryList $directoryList;

    /**
     * @var FileFactory
     */
    private FileFactory $fileFactory;

    /**
     * @var File
     */
    private File $file;

    /**
     * @param ExportInfoFactory $exportInfoFactory
     * @param ResultFactory $resultFactory
     * @param ExportManagementInterface $exportManagement
     * @param Filesystem $filesystem
     * @param DirectoryList $directoryList
     * @param FileFactory $fileFactory
     * @param File $file
     */
    public function __construct(
        ExportInfoFactory $exportInfoFactory,
        ResultFactory $resultFactory,
        ExportManagementInterface $exportManagement,
        Filesystem $filesystem,
        DirectoryList $directoryList,
        FileFactory $fileFactory,
        File $file
    ) {
        $this->exportInfoFactory = $exportInfoFactory;
        $this->resultFactory = $resultFactory;
        $this->exportManagement = $exportManagement;
        $this->filesystem = $filesystem;
        $this->directoryList = $directoryList;
        $this->fileFactory = $fileFactory;
        $this->file = $file;
    }

    /**
     * @param Export $subject
     * @param callable $proceed
     * @return Redirect
     */
    public function aroundExecute(Export $subject, callable $proceed): Redirect
    {
        $request = $subject->getRequestParameters();
        if ($request['entity'] == 'catalog_product') {
            if ($subject->getRequest()->getPost(ExportModel::FILTER_ELEMENT_GROUP)) {
                try {
                    $params = $subject->getRequestParameters();

                    if (!array_key_exists('skip_attr', $params)) {
                        $params['skip_attr'] = [];
                    }

                    $dataObject = $this->exportInfoFactory->create(
                        $params['file_format'],
                        $params['entity'],
                        $params['export_filter'],
                        $params['skip_attr']
                    );

                    $data = $this->exportManagement->export($dataObject);
                    $fileName = $dataObject->getFileName();
                    $directory = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_IMPORT_EXPORT);
                    $directory->writeFile('export/' . $fileName, $data);
                    $this->downloadProductImages();
                } catch (\Exception $e) {
                }
            }

            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('adminhtml/*/index');

            return $resultRedirect;
        } else {
            return $proceed();
        }
    }

    /**
     * @return void
     * @throws FileSystemException
     */
    private function downloadProductImages(): void
    {
        if (!class_exists('\ZipArchive')) {
            die('ZipArchive class not found');
        }

        $dir = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::ROOT);
        $rootPath = $dir.'/pub/media/catalog/product';
        chdir($rootPath);
        $zip = new \ZipArchive();
        $zip->open('product_image.zip', \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($rootPath),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file)
        {
            if (!$file->isDir())
            {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();

        $this->fileFactory->create(
            'product_image.zip',
            [
                'type' => 'filename',
                'value' => $dir.'/pub/media/catalog/product/product_image.zip',
                'rm' => true
            ],
            \Magento\Framework\App\Filesystem\DirectoryList::ROOT,
            'application/zip'
        );

        $this->file->deleteFile($dir.'/pub/media/catalog/product/product_image.zip');
    }
}
