<?php
declare(strict_types=1);

namespace Kosher\WineStore\Plugin;

use Exception;
use Kosher\WineStore\Service\CheckProductsFormCsvInDbService;
use Kosher\WineStore\Service\CreateNewCsvImportFileToSaveDbService;
use Kosher\WineStore\Service\ReadCsvProductImportFileFromTmpService;
use Magento\ImportExport\Model\Import;

class AdjustmentSourceProductFileFromTmpPlugin
{
    /**
     * @var ReadCsvProductImportFileFromTmpService
     */
    private ReadCsvProductImportFileFromTmpService $csvProductImportFileFromTmpService;

    /**
     * @var CheckProductsFormCsvInDbService
     */
    private CheckProductsFormCsvInDbService $checkProductsFormCsvInDbService;

    /**
     * @var CreateNewCsvImportFileToSaveDbService
     */
    private CreateNewCsvImportFileToSaveDbService $createNewCsvImportFileToSaveDbService;

    /**
     * @param ReadCsvProductImportFileFromTmpService $csvProductImportFileFromTmpService
     * @param CheckProductsFormCsvInDbService $checkProductsFormCsvInDbService
     * @param CreateNewCsvImportFileToSaveDbService $createNewCsvImportFileToSaveDbService
     */
    public function __construct(
        ReadCsvProductImportFileFromTmpService $csvProductImportFileFromTmpService,
        CheckProductsFormCsvInDbService $checkProductsFormCsvInDbService,
        CreateNewCsvImportFileToSaveDbService $createNewCsvImportFileToSaveDbService
    ) {
        $this->csvProductImportFileFromTmpService = $csvProductImportFileFromTmpService;
        $this->checkProductsFormCsvInDbService = $checkProductsFormCsvInDbService;
        $this->createNewCsvImportFileToSaveDbService = $createNewCsvImportFileToSaveDbService;
    }

    /**
     * @param Import $subject
     * @param string $result
     * @return string
     * @throws Exception
     */
    public function afterUploadSource(Import $subject, string $result): string
    {
        $this->checkProductsFormCsvInDbService->deleteProductUrlKey();
        $dataArray = $this->csvProductImportFileFromTmpService->execute($result);
        $dataArray = $this->checkProductsFormCsvInDbService->deleteExistSkus($dataArray);
//        $this->checkProductsFormCsvInDbService->execute($dataArray);
        $this->createNewCsvImportFileToSaveDbService->execute($result, $dataArray);

        return $result;
    }
}
