<?php
declare(strict_types=1);

namespace Kosher\ConvertProductCsvM1File\Service;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem\Driver\File;

class EditProductCsvFileService
{
    private int $countCategories = 0;
    private array $arrayData;
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
     * @return void
     * @throws FileSystemException
     */
    public function execute(string $filePath): void
    {
        $this->getCsvData($filePath);
        $this->categoriesAdjustment();
        $this->countCategoryHeaderColumns();
        $this->prepareCategoryFieldData();
        $this->prepareHeader();
        $this->file->deleteFile($filePath);
        $data = array_values($this->arrayData);
        $this->csv
            ->setEnclosure('"')
            ->setDelimiter(',')
            ->appendData($filePath, array_values($data));
    }

    /**
     * @param string $path
     * @return void
     * @throws \Exception
     */
    private function getCsvData(string $path): void
    {
        $csvData = $this->csv->getData($path);
        $result = [];
        foreach ($csvData as $row => $data) {
            if ($row == 0) {
                $result['header'] = $data;
            } else {
                if ($data[0] != '') {
                    $res = array_combine($result['header'], $data);
                    $result[] = $res;
                }
            }
        }

        $this->arrayData = $result;
    }

    /**
     * @return void
     */
    private function categoriesAdjustment(): void
    {
        foreach ($this->arrayData as $key => $data) {
            if ($key != 'header') {
                $category = $data['Categories'];
                $res = explode('</div>', $category);
                $resToArray = [];
                foreach ($res as $value) {
                    $ctgr = strip_tags($value);
                    if ($ctgr != 'Kosher4u' && $ctgr != 'Pesach') {
                        $resToArray[] = $ctgr;
                    }
                }

                $stringCategory = implode(',', $resToArray);
                $this->arrayData[$key]['Categories'] = $stringCategory;
            }
        }
    }

    /**
     * @return void
     */
    public function countCategoryHeaderColumns(): void
    {
        foreach ($this->arrayData as $data) {
            if (!empty($data['Categories'])) {
                $arrayCategories = explode(',', $data['Categories']);
                $countCategory = count($arrayCategories);
                if ($this->countCategories < $countCategory) {
                    $this->countCategories = $countCategory;
                }
            }
        }
    }

    /**
     * @return void
     */
    public function prepareCategoryFieldData(): void
    {
        unset($this->arrayData['header']['Categories']);
        foreach ($this->arrayData as $key => $rowData) {
            if (!empty($rowData['Categories'])) {
                $arrayCategories = explode(',', $rowData['Categories']);
                for ($i = 0; $i<=$this->countCategories-1; $i++) {
                    if (!empty($this->arrayData[$key]['Categories'])) {
                        unset($this->arrayData[$key]['Categories']);
                    }

                    $j = $i+1;
                    $categoryRow = 'Categories' . $j;
                    $this->arrayData[$key][$categoryRow] = '';
                    if (isset($arrayCategories[$i])) {
                        $this->arrayData[$key][$categoryRow] = $arrayCategories[$i];
                    }
                }
            }
        }
    }

    /**
     * @return void
     */
    private function prepareHeader(): void
    {
        foreach ($this->arrayData['header'] as $key => $data) {
            if ($data == 'Categories') {
                unset($this->arrayData['header'][$key]);
            }
        }

        for ($i = 1; $i<=$this->countCategories; $i++) {
            $this->arrayData['header'][] = 'Categories' . $i;
        }
    }
}
