<?php
declare(strict_types=1);

namespace Kosher\ProductAttributeSetExportImport\Service\AttributeSet;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\File\Csv;

class ExportAttributeSetToCsvService
{
    private string $fileName = 'attributeset.csv';
    /**
     * @var GetProductAttributeSetListService
     */
    private GetProductAttributeSetListService $getProductAttributeSetListService;

    /**
     * @var Csv
     */
    private Csv $csv;

    /**
     * @var DirectoryList
     */
    private DirectoryList $directoryList;

    /**
     * @var FileFactory
     */
    private FileFactory $fileFactory;

    /**
     * @param GetProductAttributeSetListService $getProductAttributeSetListService
     * @param Csv $csv
     * @param DirectoryList $directoryList
     * @param FileFactory $fileFactory
     */
    public function __construct(
        GetProductAttributeSetListService $getProductAttributeSetListService,
        Csv $csv,
        DirectoryList $directoryList,
        FileFactory $fileFactory
    ) {
        $this->getProductAttributeSetListService = $getProductAttributeSetListService;
        $this->csv = $csv;
        $this->directoryList = $directoryList;
        $this->fileFactory = $fileFactory;
    }

    /**
     * @return void
     * @throws FileSystemException
     */
    public function execute(): void
    {
        $attributeData = $this->getProductAttributeSetListService->execute();
        $filePath = $this->saveDataToCsv(array_values($attributeData));
        $this->downloadCsvFile($filePath);
    }

    /**
     * @param array $dataToCsv
     * @return string
     * @throws FileSystemException
     */
    private function saveDataToCsv(array $dataToCsv): string
    {
        $filePath = $this->directoryList->getPath(DirectoryList::VAR_DIR) . "/" . 'importexport/' . $this->fileName;
        $this->csv
            ->setEnclosure('"')
            ->setDelimiter(',')
            ->appendData($filePath, $dataToCsv);

        return $filePath;
    }

    /**
     * @param string $filePath
     * @return ResponseInterface
     * @throws \Exception
     */
    private function downloadCsvFile(string $filePath): ResponseInterface
    {
        return $this->fileFactory->create(
            basename($filePath),
            [
                'type' => 'filename',
                'value' => str_replace(BP, '', $filePath)
            ],
            DirectoryList::ROOT,
            'application/octet-stream',
            ''
        );
    }
}
