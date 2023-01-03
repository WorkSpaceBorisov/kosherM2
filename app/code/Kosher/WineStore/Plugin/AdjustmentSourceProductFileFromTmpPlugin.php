<?php
declare(strict_types=1);

namespace Kosher\WineStore\Plugin;

use Exception;
use Kosher\WineStore\Service\ImportProduct\CheckProductsFormCsvInDbService;
use Kosher\WineStore\Service\ImportProduct\ReadCsvProductImportFileFromTmpService;
use Kosher\WineStore\Service\Store\CreateNewCsvImportFileToSaveDbService;
use Magento\ImportExport\Model\Import;
use Kosher\WineStore\Service\ImportCustomer\DeleteEmptyAttributeFromCsvService;

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
     * @var DeleteEmptyAttributeFromCsvService
     */
    private DeleteEmptyAttributeFromCsvService $deleteEmptyAttributeFromCsvService;

    /**
     * @param ReadCsvProductImportFileFromTmpService $csvProductImportFileFromTmpService
     * @param CheckProductsFormCsvInDbService $checkProductsFormCsvInDbService
     * @param CreateNewCsvImportFileToSaveDbService $createNewCsvImportFileToSaveDbService
     * @param DeleteEmptyAttributeFromCsvService $deleteEmptyAttributeFromCsvService
     */
    public function __construct(
        ReadCsvProductImportFileFromTmpService $csvProductImportFileFromTmpService,
        CheckProductsFormCsvInDbService $checkProductsFormCsvInDbService,
        CreateNewCsvImportFileToSaveDbService $createNewCsvImportFileToSaveDbService,
        DeleteEmptyAttributeFromCsvService $deleteEmptyAttributeFromCsvService
    ) {
        $this->csvProductImportFileFromTmpService = $csvProductImportFileFromTmpService;
        $this->checkProductsFormCsvInDbService = $checkProductsFormCsvInDbService;
        $this->createNewCsvImportFileToSaveDbService = $createNewCsvImportFileToSaveDbService;
        $this->deleteEmptyAttributeFromCsvService = $deleteEmptyAttributeFromCsvService;
    }

    /**
     * @param Import $subject
     * @param string $result
     * @return string
     * @throws Exception
     */
    public function afterUploadSource(Import $subject, string $result): string
    {
        if ($subject->getData('entity') == 'catalog_product') {
            $this->checkProductsFormCsvInDbService->deleteProductUrlKey();
            $dataArray = $this->csvProductImportFileFromTmpService->execute($result);
            $dataArray = $this->checkProductsFormCsvInDbService->deleteExistSkus($dataArray);
            $this->createNewCsvImportFileToSaveDbService->execute($result, $dataArray);
        }
//        if ($subject->getData('entity') == 'customer') {
//            $dataArray = $this->csvProductImportFileFromTmpService->execute($result);
//            $dataArray = $this->deleteEmptyAttributeFromCsvService->execute($dataArray);
//            $this->createNewCsvImportFileToSaveDbService->execute($result, $dataArray);
//        }

        return $result;
    }
}
