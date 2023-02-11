<?php
declare(strict_types=1);

namespace Kosher\ConvertProductCsvM1File\Controller\Adminhtml\Convert;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Session\SessionManagerInterface;

class Download extends Action
{
    /**
     * @var FileFactory
     */
    private FileFactory $fileFactory;

    /**
     * @var SessionManagerInterface
     */
    private SessionManagerInterface $sessionManager;

    /**
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param SessionManagerInterface $sessionManager
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        SessionManagerInterface $sessionManager,
        ResultFactory $resultFactory
    ) {
        parent::__construct($context);
        $this->fileFactory = $fileFactory;
        $this->sessionManager = $sessionManager;
        $this->resultFactory = $resultFactory;
    }

    /**
     * @return ResponseInterface|Redirect|(Redirect&ResultInterface)|ResultInterface|void
     */
    public function execute()
    {
        try {
            $path = $this->sessionManager->getCsvFilePath();
            if ($path == null) {
                $this->messageManager->addErrorMessage('Please upload file');
                return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('/*/*');
            }

            $this->fileFactory->create(
                basename($path),
                [
                    'type' => 'filename',
                    'value' => $path
                ],
                DirectoryList::TMP
            );

            $this->sessionManager->unsCsvFilePath();

            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('/*/*');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
    }
}
