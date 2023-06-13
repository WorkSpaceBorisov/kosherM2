<?php
declare(strict_types=1);

namespace Kosher\ProductAttributeSetExportImport\Service\AttributeSet;

use Exception;
use Magento\Framework\File\Csv;
use Magento\Framework\Session\SessionManagerInterface;

class AttributeCsvFileReadService
{
    /**
     * @var Csv
     */
    private Csv $csv;

    /**
     * @var SessionManagerInterface
     */
    private SessionManagerInterface $sessionManager;

    /**
     * @param Csv $csv
     * @param SessionManagerInterface $sessionManager
     */
    public function __construct(
        Csv $csv,
        SessionManagerInterface $sessionManager
    ) {
        $this->csv = $csv;
        $this->sessionManager = $sessionManager;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function execute(): array
    {
        $path = $this->sessionManager->getAttributeCsvFilePath();
        $csvData = $this->csv->getData($path);
        $result = [];
        foreach ($csvData as $row => $data) {
            if ($row == 0) {
                $result['header'] = $data;
            } else {
                if (count($result['header']) == count($data)) {
                    $dataKey = $data[1];
                    $res = array_combine($result['header'], $data);
                    $result[$dataKey] = $res;
                }
            }
        }

        $this->sessionManager->unsAttributeCsvFilePath();

        return $result;
    }
}
