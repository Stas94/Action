<?php

namespace Puga\Action\Controller\Adminhtml\Action;

use Magento\Framework\Controller\ResultFactory;

class NewAction extends \Magento\Backend\App\Action
{
    protected $resultPageFactory = false;
    
    public function execute()
    {
        $action = $this->_objectManager->create(\Puga\Action\Model\Action::class);
        $this->_objectManager->get(\Magento\Framework\Registry::class)->register('puga_action', $action);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend((__('New Action')));

        return $resultPage;
    }
}
