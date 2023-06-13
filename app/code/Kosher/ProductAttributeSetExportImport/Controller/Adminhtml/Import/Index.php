<?php
declare(strict_types=1);

namespace Kosher\ProductAttributeSetExportImport\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Kosher_ProductAttributeSetExportImport::import';

    /**
     * @inerhitDoc
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $resultPage->getConfig()->getTitle()->prepend(__('Import Attribute Set'));

        return $resultPage;
    }
}
