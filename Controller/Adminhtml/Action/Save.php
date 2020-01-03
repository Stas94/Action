<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Puga\Action\Controller\Adminhtml\Action;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Puga\Action\Model\Action;
use Magento\Framework\Exception\LocalizedException;

/**
 * Save Action page action.
 */
class Save extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Puga_Action::save';

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Puga\Action\Model\ActionFactory
     */
    private $pageFactory;

    /**
     * @var \Puga\Action\Api\ActionRepositoryInterface
     */
    private $pageRepository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param \Puga\Action\Model\ActionFactory|null $pageFactory
     * @param \Puga\Action\Api\ActionRepositoryInterface|null $pageRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        DataPersistorInterface $dataPersistor,
        \Puga\Action\Model\ActionFactory $pageFactory = null
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->pageFactory = $pageFactory
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(\Puga\Action\Model\ActionFactory::class);
        parent::__construct($context);
    }
    /**
     * Save action
     *
     * @param \Puga\Action\Model\Action
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
//            $data = $this->dataProcessor->filter($data);
            if (isset($data['is_active']) && $data['is_active'] === 'true') {
                $data['is_active'] = Action::STATUS_ENABLED;
            }
            if (empty($data['id'])) {
                $data['id'] = null;
            }

            /** @var \Puga\Action\Model\Action $model */
            $model = $this->pageFactory->create();

            $id = $this->getRequest()->getParam('id');
            if ($id) {
                try {
                    $model = $model->load($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This action no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            $model->setData($data);

            $this->_eventManager->dispatch(
                'puga_action_prepare_save',
                ['page' => $model, 'request' => $this->getRequest()]
            );

            try {
//                $this->pageRepository->save($model);
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the page.'));
                return $this->processResultRedirect($model, $resultRedirect, $data);
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the page.'));
            }

            $this->dataPersistor->set('puga_action', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Process result redirect
     *
     * @param \Magento\Cms\Api\Data\PageInterface $model
     * @param \Magento\Backend\Model\View\Result\Redirect $resultRedirect
     * @param array $data
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws LocalizedException
     */
    private function processResultRedirect($model, $resultRedirect, $data)
    {
        if ($this->getRequest()->getParam('back', false) === 'duplicate') {
            $newPage = $this->pageFactory->create(['data' => $data]);
            $newPage->setId(null);
            $newPage->setIsActive(false);
            $this->pageRepository->save($newPage);
            $this->messageManager->addSuccessMessage(__('You duplicated the page.'));
            return $resultRedirect->setPath(
                '*/*/edit',
                [
                    'id' => $newPage->getId(),
                    '_current' => true
                ]
            );
        }
        $this->dataPersistor->clear('puga_action');
        if ($this->getRequest()->getParam('back')) {
            return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
