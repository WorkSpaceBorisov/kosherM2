<?php
declare(strict_types=1);

namespace Kosher\ConvertProductCsvM1File\Controller\Adminhtml\Convert;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    const ADMIN_RESOURCE = 'Kosher_ConvertProductCsvM1File::convert';

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Product CSV file From M1'));

        return $resultPage;
    }
}
