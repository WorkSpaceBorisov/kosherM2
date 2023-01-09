<?php
declare(strict_types=1);

namespace Kosher\WineStore\Setup\Patch\Data;

use Kosher\WineStore\Service\ImportProduct\AddAttributeServiceService;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class CreateAttributeSetWineDataPatch implements DataPatchInterface
{
    private AddAttributeServiceService $addAttributeServiceService;

    /**
     * @param AddAttributeServiceService $addAttributeServiceService
     */
    public function __construct(
        AddAttributeServiceService $addAttributeServiceService
    ) {
        $this->addAttributeServiceService = $addAttributeServiceService;
    }

    /**
     * @return CreateAttributeSetWineDataPatch|$this
     * @throws LocalizedException
     */
    public function apply(): CreateAttributeSetWineDataPatch|static
    {
        $this->addAttributeServiceService->execute('Wine');
        $this->addAttributeServiceService->execute('Spirit');
        $this->addAttributeServiceService->execute('Liqueurs');
        $this->addAttributeServiceService->execute('Whisky');
        $this->addAttributeServiceService->execute('Cognac');
        $this->addAttributeServiceService->execute('Cocktails');

        return $this;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
