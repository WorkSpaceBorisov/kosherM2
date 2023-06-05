<?php

namespace Kosher\ProductPopup\Api;

interface PopupProductDataInterface
{
    /**
     * @param string $sku
     * @return string
     */
    public function get(string $sku): string;
}
