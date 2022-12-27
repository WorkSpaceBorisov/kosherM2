<?php
declare(strict_types=1);

namespace Kosher\WineStore\Service;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem\Driver\File;

class CreateNewCsvImportFileToSaveDbService
{
    /**
     * @var File
     */
    private File $file;

    /**
     * @var Csv
     */
    private Csv $csv;

    /**
     * @param File $file
     * @param Csv $csv
     */
    public function __construct(
        File $file,
        Csv $csv
    ) {
        $this->file = $file;
        $this->csv = $csv;
    }

    /**
     * @param string $filePath
     * @param array $data
     * @return void
     * @throws FileSystemException
     */
    public function execute(string $filePath, array $data): void
    {
        $data = array_values($data);
        $this->file->deleteFile($filePath);
        $this->csv
            ->setEnclosure('"')
            ->setDelimiter(',')
            ->appendData($filePath, $data);
    }
}
