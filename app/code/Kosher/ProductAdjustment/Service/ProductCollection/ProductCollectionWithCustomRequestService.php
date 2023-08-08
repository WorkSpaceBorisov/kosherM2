<?php
declare(strict_types=1);

namespace Kosher\ProductAdjustment\Service\ProductCollection;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\App\RequestInterface;
use PharIo\Manifest\ElementCollectionException;

class ProductCollectionWithCustomRequestService
{
    const REQUEST_PARAM = 'show';
    const SALE = 'sale';
    const SPECIAL_PRICE = 'special_price';
    const SPECIAL_TO_DATE = 'special_to_date';
    const SPECIAL_FROM_DATE = 'special_from_date';
    const HOLIDAYS_DEAL = 'holidays';
    const HOLIDAYS_DEAL_FROM = 'dv_deal_from';
    const HOLIDAYS_DEAL_TO = 'dv_deal_to';
    const NEW = 'new';
    const NEWS_FROM_DATE = 'news_from_date';
    const NEWS_TO_DATE = 'news_to_date';

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * @param Collection $productCollection
     * @return Collection
     */
    public function execute(Collection $productCollection): Collection
    {
        $requestParam = $this->request->getParam(self::REQUEST_PARAM);
        if (!empty($requestParam)) {
            try {
                if ($requestParam == self::SALE) {
                    $productCollection->addAttributeToFilter(self::SPECIAL_PRICE, ['notnull' => true]);
                }

                if ($requestParam == self::HOLIDAYS_DEAL) {
                    $productCollection->addAttributeToFilter([['attribute' => self::HOLIDAYS_DEAL_FROM, 'notnull' => true],
                        ['attribute' => self::HOLIDAYS_DEAL_TO, 'notnull' => true]]);
                }

                if ($requestParam == self::NEW) {
                    $productCollection->addAttributeToFilter([['attribute' => self::NEWS_FROM_DATE, 'notnull' => true],
                        ['attribute' => self::NEWS_TO_DATE, 'notnull' => true]]);
                }

                return $productCollection;
            } catch (\Exception $e) {
                throw new ElementCollectionException($e->getMessage());
            }
        }

        return $productCollection;
    }
}
