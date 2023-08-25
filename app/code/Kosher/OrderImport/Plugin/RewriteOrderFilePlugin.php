<?php
declare(strict_types=1);

namespace Kosher\OrderImport\Plugin;

use Kosher\OrderImport\Service\OrderFile\OrderXmlFileAdjustmentService;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\File\Uploader;

class RewriteOrderFilePlugin
{
    /**
     * @var OrderXmlFileAdjustmentService
     */
    private OrderXmlFileAdjustmentService $orderXmlFileAdjustmentService;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @param OrderXmlFileAdjustmentService $orderXmlFileAdjustmentService
     * @param RequestInterface $request
     */
    public function __construct(
        OrderXmlFileAdjustmentService $orderXmlFileAdjustmentService,
        RequestInterface $request
    ) {
        $this->orderXmlFileAdjustmentService = $orderXmlFileAdjustmentService;
        $this->request = $request;
    }

    /**
     * @param Uploader $subject
     * @param array $result
     * @param $destinationFolder
     * @param $newFileName
     * @return array
     * @throws FileSystemException
     */
    public function afterSave(Uploader $subject, array $result, $destinationFolder, $newFileName = null): array
    {
        $routeName =  $this->request->getRouteName();
        $actionName =  $this->request->getActionName();
        if ($routeName == 'ordersexportimport' && $actionName == 'upload') {
            $this->orderXmlFileAdjustmentService->execute($result);
        }

        return $result;
    }
}
