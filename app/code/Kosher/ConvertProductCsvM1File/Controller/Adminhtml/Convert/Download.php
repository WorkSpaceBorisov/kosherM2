<?php
declare(strict_types=1);

namespace Kosher\ConvertProductCsvM1File\Controller\Adminhtml\Convert;

use Kosher\ConvertProductCsvM1File\Service\EditProductCsvFileService;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Download extends Action
{
    const ADMIN_RESOURCE = 'Kosher_ConvertProductCsvM1File::convert';
    /**
     * @var FileFactory
     */
    private FileFactory $fileFactory;

    /**
     * @var EditProductCsvFileService
     */
    private EditProductCsvFileService $editProductCsvFileService;

    /**
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param ResultFactory $resultFactory
     * @param EditProductCsvFileService $editProductCsvFileService
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        ResultFactory $resultFactory,
        EditProductCsvFileService $editProductCsvFileService
    ) {
        parent::__construct($context);
        $this->fileFactory = $fileFactory;
        $this->resultFactory = $resultFactory;
        $this->editProductCsvFileService = $editProductCsvFileService;
    }

    /**
     * @return ResponseInterface|Redirect|(Redirect&ResultInterface)|ResultInterface|void
     */
    public function execute()
    {
        try {
            $request = $this->getRequest()->getParams();
            $checkSingColumn = (int)$request['single_column_select'];
            $fileData = $request['file_name'][0];
            $path = $fileData['path'] . $fileData['file'];
            if ($path == null) {
                $this->messageManager->addErrorMessage('Please upload file');
                return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('/*/*');
            }

            $this->editProductCsvFileService->execute($path, $checkSingColumn);
            $this->downloadFile($path);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
    }

    private function downloadFile(string $path)
    {
        $this->fileFactory->create(
            basename($path),
            [
                    'type' => 'filename',
                    'value' => $path
                ],
            DirectoryList::TMP
        );
    }
}
