<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Puga\Action\Controller\Adminhtml\Action;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;

/**
 * Delete CMS page action.
 */
class Delete extends \Magento\Backend\App\Action implements HttpPostActionInterface
{

    /**
     * @var \Puga\Action\Api\ActionRepositoryInterface
     */
    private $actionRepository;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param \Puga\Action\Api\ActionRepositoryInterface|null $actionRepository
     */
    public function __construct(
        Action\Context $context,
        \Puga\Action\Api\ActionRepositoryInterface $actionRepository = null
    ) {
        $this->actionRepository = $actionRepository
            ?: \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Puga\Action\Api\ActionRepositoryInterface::class);
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($id) {
            try {
                $model = $this->_objectManager->create(\Puga\Action\Model\Action::class);
                $model->load($id);

                $this->actionRepository->delete($model);
                $this->messageManager->addSuccessMessage(__('The page has been deleted.'));
                $this->_eventManager->dispatch('adminhtml_pugaaction_on_delete', [
                    'status' => 'success'
                ]);

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a page to delete.'));

        return $resultRedirect->setPath('*/*/');
    }
}
