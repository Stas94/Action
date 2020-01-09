<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Puga\Action\Controller\Adminhtml\Action;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Puga\Action\Model\ImageUploader;

/**
 * Save CMS page action.
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
     * @var \Puga\Action\Model\ActionFactory
     */
    private $pageFactory;

    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * @var \Puga\Action\Api\ActionRepositoryInterface
     */
    private $pageRepository;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param \Puga\Action\Model\ActionFactory|null $pageFactory
     * @param \Puga\Action\Api\ActionRepositoryInterface|null $pageRepository
     * @param \Puga\Action\Model\ActionFactory $imageUpload = null
     */
    public function __construct(
        Action\Context $context,
        \Puga\Action\Model\ActionFactory $pageFactory = null,
        \Puga\Action\Api\ActionRepositoryInterface $pageRepository = null,
        \Puga\Action\Model\ActionFactory $imageUpload = null
    ) {
        $this->imageUploader = $imageUpload
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(\Puga\Action\Model\ImageUploader::class);
        $this->pageFactory = $pageFactory
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(\Puga\Action\Model\ActionFactory::class);
        $this->pageRepository = $pageRepository
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
            if (isset($data['is_active']) && $data['is_active'] === 'true') {
                $data['is_active'] = \Puga\Action\Model\Action::STATUS_ENABLED;
            }
            if (empty($data['id'])) {
                $data['id'] = null;
            }

            if(isset($data['image'])) {
                if (is_array($data['image'])) {
                    $fileName = $data['image'][0]['name'];
                    $data['image'] = $fileName;
                    $this->imageUploader->moveFileFromTmp($fileName);
                }
            }

            /** @var \Puga\Action\Model\Action $model */
            $model = $this->pageFactory->create();

            $id = $this->getRequest()->getParam('id');
            if ($id) {
                try {
                    $model = $this->pageRepository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This page no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            $model->setData($data);

            $this->_eventManager->dispatch(
                'puga_action_prepare_save',
                ['page' => $model, 'request' => $this->getRequest()]
            );

            try {
                $this->pageRepository->save($model);
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
        if ($this->getRequest()->getParam('back')) {
            return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
