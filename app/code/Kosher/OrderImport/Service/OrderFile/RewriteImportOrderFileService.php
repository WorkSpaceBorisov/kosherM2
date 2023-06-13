<?php
declare(strict_types=1);

namespace Kosher\OrderImport\Service\OrderFile;

use Aitoc\OrdersExportImport\Model\Processor\Container\Xml;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Driver\File as Files;
use Aitoc\OrdersExportImport\Model\Processor\Converter;

class RewriteImportOrderFileService extends Xml
{

    /**
     * @var Files
     */
    private Files $files;

    /**
     * @var Converter
     */
    private Converter $converter;


    /**
     * @param Files $files
     * @param Converter $converter
     */
    public function __construct(
        Files $files,
        Converter $converter
    ) {
        $this->files = $files;
        $this->converter = $converter;
    }

    /**
     * @param string $filePath
     * @param array $orderImportData
     * @return void
     * @throws FileSystemException
     */
    public function execute(string $filePath, array $orderImportData): void
    {
        $this->files->deleteFile($filePath);
        $this->file = $filePath;
        $this->recordNode = 'order';
        $this->mainNode = 'records';
        $this->addFileBeginning();
        foreach ($orderImportData as $item) {
            $item = $this->converter->apply($item);
            $this->append($item);
        }
    }
}
