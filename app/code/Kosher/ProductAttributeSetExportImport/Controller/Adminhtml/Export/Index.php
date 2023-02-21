<?php
declare(strict_types=1);

namespace Kosher\ProductAttributeSetExportImport\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Kosher\ProductAttributeSetExportImport\Service\AttributeSet\ExportAttributeSetToCsvService;

class Index extends Action
{
    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;
    private ExportAttributeSetToCsvService $attributeSetToCsvService;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ExportAttributeSetToCsvService $attributeSetToCsvService
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->attributeSetToCsvService = $attributeSetToCsvService;
    }

    const ADMIN_RESOURCE = 'Kosher_ProductAttributeSetExportImport::export';

    public function execute()
    {
        $this->attributeSetToCsvService->execute();
//        $resultPage = $this->resultPageFactory->create();
//
//        return $resultPage;
    }
}
