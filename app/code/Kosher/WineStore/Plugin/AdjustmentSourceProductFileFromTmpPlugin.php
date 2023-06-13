<?php
declare(strict_types=1);

namespace Kosher\WineStore\Plugin;

use Exception;
use Kosher\WineStore\Service\ImportCustomer\DeleteEmptyAttributeFromCsvService;
use Kosher\WineStore\Service\ImportProduct\CheckProductsFormCsvInDbService;
use Kosher\WineStore\Service\ImportProduct\ReadCsvProductImportFileFromTmpService;
use Kosher\WineStore\Service\Store\CreateNewCsvImportFileToSaveDbService;
use Magento\ImportExport\Model\Import;
use Kosher\WineStore\Service\ImportCustomerAddress\ReadCsvCustomerAddressService;

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
     * @var ReadCsvCustomerAddressService
     */
    private ReadCsvCustomerAddressService $csvCustomerAddressService;

    /**
     * @param ReadCsvProductImportFileFromTmpService $csvProductImportFileFromTmpService
     * @param CheckProductsFormCsvInDbService $checkProductsFormCsvInDbService
     * @param CreateNewCsvImportFileToSaveDbService $createNewCsvImportFileToSaveDbService
     * @param DeleteEmptyAttributeFromCsvService $deleteEmptyAttributeFromCsvService
     * @param ReadCsvCustomerAddressService $csvCustomerAddressService
     */
    public function __construct(
        ReadCsvProductImportFileFromTmpService $csvProductImportFileFromTmpService,
        CheckProductsFormCsvInDbService $checkProductsFormCsvInDbService,
        CreateNewCsvImportFileToSaveDbService $createNewCsvImportFileToSaveDbService,
        DeleteEmptyAttributeFromCsvService $deleteEmptyAttributeFromCsvService,
        ReadCsvCustomerAddressService $csvCustomerAddressService
    ) {
        $this->csvProductImportFileFromTmpService = $csvProductImportFileFromTmpService;
        $this->checkProductsFormCsvInDbService = $checkProductsFormCsvInDbService;
        $this->createNewCsvImportFileToSaveDbService = $createNewCsvImportFileToSaveDbService;
        $this->deleteEmptyAttributeFromCsvService = $deleteEmptyAttributeFromCsvService;
        $this->csvCustomerAddressService = $csvCustomerAddressService;
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
        if ($subject->getData('entity') == 'customer') {
            $dataArray = $this->csvProductImportFileFromTmpService->execute($result);
            $dataArray = $this->deleteEmptyAttributeFromCsvService->execute($dataArray);
            $this->createNewCsvImportFileToSaveDbService->execute($result, $dataArray);
        }

        if ($subject->getData('entity') == 'customer_address') {
            $dataArray = $this->csvCustomerAddressService->execute($result);
            $this->createNewCsvImportFileToSaveDbService->execute($result, $dataArray);
        }

        return $result;
    }
}
