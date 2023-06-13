<?php
declare(strict_types=1);

namespace Kosher\WineStore\Setup\Patch\Data;

use Kosher\WineStore\Service\Store\CreateWineStoreService;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class CreateWineStoreDataPatch implements DataPatchInterface
{
    /**
     * @var CreateWineStoreService
     */
    private CreateWineStoreService $createWineStoreService;

    /**
     * @param CreateWineStoreService $createWineStoreService
     */
    public function __construct(
        CreateWineStoreService $createWineStoreService
    ) {
        $this->createWineStoreService = $createWineStoreService;
    }

    /**
     * @return CreateWineStoreDataPatch|$this
     * @throws AlreadyExistsException
     * @throws LocalizedException
     */
    public function apply(): CreateWineStoreDataPatch|static
    {
        $this->createWineStoreService->execute();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
