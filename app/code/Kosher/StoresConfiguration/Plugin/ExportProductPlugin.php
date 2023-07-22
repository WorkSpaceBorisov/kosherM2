<?php
declare(strict_types=1);

namespace Kosher\StoresConfiguration\Plugin;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
use Magento\ImportExport\Controller\Adminhtml\Export\Export;
use Magento\ImportExport\Model\Export as ExportModel;
use Magento\ImportExport\Model\Export\Entity\ExportInfoFactory;
use Magento\ImportExport\Api\ExportManagementInterface;
use Magento\Framework\Filesystem;

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
     * @param ExportInfoFactory $exportInfoFactory
     * @param ResultFactory $resultFactory
     * @param ExportManagementInterface $exportManagement
     * @param Filesystem $filesystem
     */
    public function __construct(
        ExportInfoFactory $exportInfoFactory,
        ResultFactory $resultFactory,
        ExportManagementInterface $exportManagement,
        Filesystem $filesystem
    ) {
        $this->exportInfoFactory = $exportInfoFactory;
        $this->resultFactory = $resultFactory;
        $this->exportManagement = $exportManagement;
        $this->filesystem = $filesystem;
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
}
