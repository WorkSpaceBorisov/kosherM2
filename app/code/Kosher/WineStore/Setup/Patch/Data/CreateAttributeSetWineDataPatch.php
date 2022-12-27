<?php
declare(strict_types=1);

namespace Kosher\WineStore\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Kosher\WineStore\Service\AddAttributeServiceService;

class CreateAttributeSetWineDataPatch implements DataPatchInterface
{
    private AddAttributeServiceService $addAttributeServiceService;

    public function __construct(
        AddAttributeServiceService $addAttributeServiceService
    ){
        $this->addAttributeServiceService = $addAttributeServiceService;
    }
    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply()
    {
        $this->addAttributeServiceService->execute('Wine');
        $this->addAttributeServiceService->execute('Spirit');
        $this->addAttributeServiceService->execute('Liqueurs');
        $this->addAttributeServiceService->execute('Whisky');
        $this->addAttributeServiceService->execute('Cognac');
        $this->addAttributeServiceService->execute('Cocktails');
        return $this;
    }
}
