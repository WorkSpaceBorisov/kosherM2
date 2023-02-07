<?php
declare(strict_types=1);

namespace Kosher\OrderImport\Plugin;

use Kosher\OrderImport\Service\OrderFile\OrderXmlFileAdjustmentService;
use Magento\Framework\File\Uploader;

class RewriteOrderFilePlugin
{
    /**
     * @var OrderXmlFileAdjustmentService
     */
    private OrderXmlFileAdjustmentService $orderXmlFileAdjustmentService;

    /**
     * @param OrderXmlFileAdjustmentService $orderXmlFileAdjustmentService
     */
    public function __construct(
        OrderXmlFileAdjustmentService $orderXmlFileAdjustmentService
    ) {
        $this->orderXmlFileAdjustmentService = $orderXmlFileAdjustmentService;
    }
    /**
     * @param Uploader $subject
     * @param array $result
     * @param string $destinationFolder
     * @param string|null $newFileName
     * @return array
     */
    public function afterSave(Uploader $subject, array $result, $destinationFolder, $newFileName = null): array
    {
        $this->orderXmlFileAdjustmentService->execute($result);
        return $result;
    }
}
