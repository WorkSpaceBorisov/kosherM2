<?php
declare(strict_types=1);

namespace Kosher\ProductAttributeSetExportImport\Controller\Adminhtml\Export;

use Kosher\ProductAttributeSetExportImport\Service\AttributeSet\ExportAttributeSetToCsvService;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;

class Index extends Action
{
    /**
     * @var ExportAttributeSetToCsvService
     */
    private ExportAttributeSetToCsvService $attributeSetToCsvService;

    const ADMIN_RESOURCE = 'Kosher_ProductAttributeSetExportImport::export';

    /**
     * @param Context $context
     * @param ExportAttributeSetToCsvService $attributeSetToCsvService
     */
    public function __construct(
        Context $context,
        ExportAttributeSetToCsvService $attributeSetToCsvService
    ) {
        parent::__construct($context);
        $this->attributeSetToCsvService = $attributeSetToCsvService;
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws FileSystemException
     */
    public function execute()
    {
        $this->attributeSetToCsvService->execute();
    }
}
