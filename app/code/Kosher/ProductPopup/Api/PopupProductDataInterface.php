<?php

namespace Kosher\ProductPopup\Api;

interface PopupProductDataInterface
{
    const API_PREFIX = 'rest';
    const GET_PRODUCT_BY_SKU_REQUEST = '/V1/product_popup/';

    /**
     * @param string $sku
     * @return string
     */
    public function get(string $sku): string;
}
