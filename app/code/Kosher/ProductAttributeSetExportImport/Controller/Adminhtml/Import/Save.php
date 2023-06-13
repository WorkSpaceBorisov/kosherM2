<?php
declare(strict_types=1);

namespace Kosher\ProductAttributeSetExportImport\Controller\Adminhtml\Import;

use Exception;
use Kosher\ProductAttributeSetExportImport\Service\AttributeSet\AttributeSetSaveProcessService;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Save extends Action
{
    /**
     * @var AttributeSetSaveProcessService
     */
    private AttributeSetSaveProcessService $attributeSetSaveProcessService;

    /**
     * @param Context $context
     * @param AttributeSetSaveProcessService $attributeSetSaveProcessService
     */
    public function __construct(
        Context $context,
        AttributeSetSaveProcessService $attributeSetSaveProcessService
    ) {
        parent::__construct($context);
        $this->attributeSetSaveProcessService = $attributeSetSaveProcessService;
    }

    const ADMIN_RESOURCE = 'Kosher_ProductAttributeSetExportImport::import';

    /**
     * @inerhitDoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        try {
            $this->attributeSetSaveProcessService->execute();
        } catch (Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());

            return $resultRedirect->setPath('*/*/');
        }

        $this->messageManager->addSuccessMessage('Attribute set was imported');

        return $resultRedirect->setPath('*/*/');
    }
}
