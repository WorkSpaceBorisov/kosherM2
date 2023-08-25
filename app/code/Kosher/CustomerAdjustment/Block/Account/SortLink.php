<?php

declare(strict_types=1);

namespace Kosher\CustomerAdjustment\Block\Account;

use Magento\Customer\Block\Account\SortLink as MagentoSortLink;
use Magento\Framework\App\DefaultPathInterface;
use Magento\Framework\View\Element\Template\Context;

class SortLink extends MagentoSortLink
{
    /**
     * @param Context              $context
     * @param DefaultPathInterface $defaultPath
     * @param array                $data
     */
    public function __construct(
        Context $context,
        DefaultPathInterface $defaultPath,
        array $data = []
    ) {
        parent::__construct($context, $defaultPath, $data);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        if (false != $this->getTemplate()) {
            return parent::_toHtml();
        }

        if ($this->isCurrent() && $attributeHtml = $this->getAttributesHtml()) {
            $html = '<li class="nav item current">';
            $html .= '<strong ' . $attributeHtml . '>'
                . $this->escapeHtml(__($this->getLabel()))
                . '</strong>';
            $html .= '</li>';
        } else {
            $html = parent::_toHtml();
        }

        return $html;
    }

    /**
     * Generate attributes' HTML code
     *
     * @return string
     */
    private function getAttributesHtml(): string
    {
        $attributesHtml = '';
        $attributes = $this->getAttributes();
        if ($attributes) {
            foreach ($attributes as $attribute => $value) {
                $attributesHtml .= ' ' . $attribute . '="' . $this->escapeHtml($value) . '"';
            }
        }

        return $attributesHtml;
    }
}