<?php
namespace Infomodus\Wine\Controller\Adminhtml;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

abstract class Wine extends Action
{
    /**
     * Initialize Group Controller
     *
     * @param Context $context
     */
    public function __construct(
        Context $context
    )
    {
        parent::__construct($context);
    }
}
