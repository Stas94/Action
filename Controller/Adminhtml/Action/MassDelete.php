<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Puga\Action\Controller\Adminhtml\Action;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Puga\Action\Model\ResourceModel\Action\CollectionFactory;
use Puga\Action\Api\ActionRepositoryInterface;

/**
 * Class MassDelete
 */
class MassDelete extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Puga_Action::puga_action_delete';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var ActionRepositoryInterface
     */
    private $actionRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param ActionRepositoryInterface $actionRepository
     */
    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory, ActionRepositoryInterface $actionRepository = null)
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->actionRepository = $actionRepository
            ?: \Magento\Framework\App\ObjectManager::getInstance()->create(ActionRepositoryInterface::class);
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        /** @var \Puga\Action\Model\Action $page */
        foreach ($collection as $page) {
            $data = $page->getData();
            $this->actionRepository->delete($data);
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
