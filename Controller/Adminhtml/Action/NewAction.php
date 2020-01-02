<?php

namespace Puga\Action\Controller\Adminhtml\Action;

use Magento\Framework\Controller\ResultFactory;

class NewAction extends \Magento\Backend\App\Action
{
    protected $resultPageFactory = false;
    
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend((__('New Action')));

        return $resultPage;
    }
}
