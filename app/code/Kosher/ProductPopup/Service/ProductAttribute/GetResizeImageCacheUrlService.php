<?php
declare(strict_types=1);

namespace Kosher\ProductPopup\Service\ProductAttribute;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Image as ImageHelper;

class GetResizeImageCacheUrlService
{
    /**
     * @var ImageHelper
     */
    private ImageHelper $imageHelper;

    /**
     * @param ImageHelper $imageHelper
     */
    public function __construct(
        ImageHelper $imageHelper
    ) {
        $this->imageHelper = $imageHelper;
    }

    /**
     * @param ProductInterface $product
     *
     * @return array
     */
    public function execute(ProductInterface $product): array
    {
        $image = $this->imageHelper->init($product, 'product_base_image')->setImageFile($product->getImage())->getUrl();
        $smallImage = $this->imageHelper->init($product, 'product_small_image')->setImageFile($product->getImage())->getUrl();
        $thumbnail = $this->imageHelper->init($product, 'product_page_image_small')->setImageFile($product->getImage())->getUrl();

        return [
            'image' => $image,
            'small_image' => $smallImage,
            'thumbnail' => $thumbnail,
        ];
    }
}
