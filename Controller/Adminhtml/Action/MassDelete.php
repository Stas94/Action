<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Puga\Action\Controller\Adminhtml\Action;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Puga\Action\Model\ActionFactory;
use Magento\Framework\Registry;
use Puga\Action\Model\ResourceModel\Action\CollectionFactory;
use Puga\Action\Model\ResourceModel\Action\Collection;

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
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Collection
     */
    private $collection;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param CollectionFactory $collectionFactory
     * @param ActionFactory $reviewFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        ActionFactory $reviewFactory,
        CollectionFactory $collectionFactory
    )
    {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $reviewsIds = $this->getRequest()->getParam('selected');
        if (!is_array($reviewsIds)) {
            $this->messageManager->addError(__('Please select review(s).'));
        } else {
            try {
                foreach ($this->getCollection() as $model) {
//                    $collectionSize = $this->getCollection()->getSize();
                    $model->delete();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($reviewsIds))
                );
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while deleting these records.'));
            }
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.'));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Returns requested collection.
     *
     * @return Collection
     */
    private function getCollection(): Collection
    {
        if ($this->collection === null) {
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter(
                'main_table.' . $collection->getResource()
                    ->getIdFieldName(),
                $this->getRequest()->getParam('selected')
            );

            $this->collection = $collection;
        }

        return $this->collection;
    }
}
