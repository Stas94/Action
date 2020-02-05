<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Puga\Action\Model;

use Puga\Action\Api\ActionRepositoryInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Puga\Action\Model\ResourceModel\Action as ResourceAction;
use Magento\Store\Model\StoreManagerInterface;
use Puga\Action\Api\Data\ActionInterface;

/**
 * Class ActionRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ActionRepository implements ActionRepositoryInterface
{
    /**
     * @var ResourceAction
     */
    protected $resource;

    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ResourceAction $resource
     * @param ActionFactory $actionFactory
     * @param StoreManagerInterface $storeManager
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ResourceAction $resource,
        ActionFactory $actionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->actionFactory = $actionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Save Page data
     *
     * @param ActionInterface|Action $page
     * @return Action
     * @throws CouldNotSaveException
     */
    public function save(ActionInterface $page)
    {
        if ($page->getStoreId() === null) {
            $storeId = $this->storeManager->getStore()->getId();
            $page->setStoreId($storeId);
        }
        try {
           $this->resource->save($page);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the page: %1', $exception->getMessage()),
                $exception
            );
        }
        return $page;
    }

    /**
     * Load Page data by given Page Identity
     *
     * @param string $pageId
     * @return Action
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($pageId)
    {
        $page = $this->actionFactory->create();
        $page->load($pageId);
        if (!$page->getId()) {
            throw new NoSuchEntityException(__('The Action with the "%1" ID doesn\'t exist.', $pageId));
        }
        return $page;
    }

    /**
     * Delete Page
     *
     * @param ActionInterface|Action $page
     * @return Action
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(ActionInterface $page)
    {
        try {
            $this->resource->delete($page);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete the page: %1', $exception->getMessage())
            );
        }
        return $page;
    }

    /**
     * Delete Page by given Page Identity
     *
     * @param string $pageId
     * @return string
     */
    public function deleteById($pageId)
    {
        return $this->delete($this->getById($pageId));
    }
}
