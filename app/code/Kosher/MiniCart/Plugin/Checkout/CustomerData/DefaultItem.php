<?php

namespace Kosher\MiniCart\Plugin\Checkout\CustomerData;

class DefaultItem
{
    public function aroundGetItemData(
        \Magento\Checkout\CustomerData\AbstractItem $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item $item
    ) {
        $data = $proceed($item);
        $result['singleweight'] = $item->getProduct()->getAttributeText('singleweight');
        return \array_merge(
            $result,
            $data
        );
    }
}