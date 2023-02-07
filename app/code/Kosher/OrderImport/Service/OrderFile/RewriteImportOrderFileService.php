<?php
declare(strict_types=1);

namespace Kosher\OrderImport\Service\OrderFile;

use Aitoc\OrdersExportImport\Model\Processor\Container\Xml;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Driver\File as Files;

class RewriteImportOrderFileService extends Xml
{

    /**
     * @var Files
     */
    private Files $files;

    /**
     * @param Files $files
     */
    public function __construct(
        Files $files
    ) {
        $this->files = $files;
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
            $this->append($item);
        }
    }
}
