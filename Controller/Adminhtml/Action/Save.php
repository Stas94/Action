<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Puga\Action\Controller\Adminhtml\Action;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;

/**
 * Save CMS page action.
 */
class Save extends Action
{
    /**
     * @var \Puga\Action\Model\ActionFactory
     */
    private $actionFactory;

    /**
     * @var \Puga\Action\Api\ActionRepositoryInterface
     */
    private $actionRepository;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param \Puga\Action\Model\ActionFactory|null $actionFactory
     * @param \Puga\Action\Api\ActionRepositoryInterface|null $actionRepository
     */
    public function __construct(
        Action\Context $context,
        \Puga\Action\Model\ActionFactory $actionFactory = null,
        \Puga\Action\Api\ActionRepositoryInterface $actionRepository = null
    ) {
        $this->actionFactory = $actionFactory
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(\Puga\Action\Model\ActionFactory::class);
        $this->actionRepository = $actionRepository
            ?: \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Puga\Action\Api\ActionRepositoryInterface::class);
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            if (empty($data['id'])) {
                $data['id'] = null;
            }

            if (isset($data['image'])) {
                $fileName = $data['image'][0]['name'];
                $data['image'] = $fileName;
            }

            /** @var \Puga\Action\Model\Action $model */
            $model = $this->actionFactory->create();

            $id = $this->getRequest()->getParam('id');
            if ($id) {
                try {
                    $model = $this->actionRepository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This page no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            $model->setData($data);

            if (isset($data['action_products'])
                && is_string($data['action_products'])
            ) {
                $products = json_decode($data['action_products'], true);
                $model->setPostedProducts($products);
            }

            try {
                $this->actionRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the action.'));
                return $this->processResultRedirect($model, $resultRedirect, $data);
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the page.'));
            }
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Process result redirect
     *
     * @param \Puga\Action\Api\Data\ActionInterface $model
     * @param \Magento\Backend\Model\View\Result\Redirect $resultRedirect
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws LocalizedException
     */
    private function processResultRedirect($model, $resultRedirect)
    {
        if ($this->getRequest()->getParam('back')) {
            return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
