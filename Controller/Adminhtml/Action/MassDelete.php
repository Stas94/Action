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
     * Massactions filter
     *
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
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ActionRepositoryInterface $actionRepository = null
    )
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
        $productDeleted = 0;
        /** @var \Puga\Action\Model\Action $action */
        foreach ($collection->getItems() as $action) {
            $this->actionRepository->delete($action);
            $productDeleted++;
        }

        if ($productDeleted) {
            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been deleted.', $productDeleted)
            );
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/');
    }
}
