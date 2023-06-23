<?php

namespace Kosher\CustomerAdjustment\ViewModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Customer\Model\Session as CustomerSession;

class CustomerLoginViewModel implements ArgumentInterface
{
    /**
     * @var CustomerSession
     */
    private CustomerSession $customerSession;

    /**
     * @param CustomerSession $customerSession
     */
    public function __construct(
        CustomerSession $customerSession
    ) {
        $this->customerSession = $customerSession;
    }

    /**
     * @return bool
     */
    public function isCustomerLogin(): bool
    {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getCustomerName(): string
    {
        return $this->customerSession->getCustomer()->getName();
    }
}
