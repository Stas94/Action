<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Puga\Action\Model;

use Puga\Action\Api\Data;
use Puga\Action\Api\ActionRepositoryInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Puga\Action\Model\ResourceModel\Action as ResourceAction;
use Puga\Action\Model\ResourceModel\Action\CollectionFactory as ActionCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use \Puga\Action\Api\Data\ActionInterface;

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
    protected $pageFactory;

    /**
     * @var ActionCollectionFactory
     */
    protected $pageCollectionFactory;

    /**
     * @var Data\ActionSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Puga\Action\Api\Data\ActionInterfaceFactory
     */
    protected $dataPageFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ResourceAction $resource
     * @param ActionFactory $pageFactory
     * @param Data\ActionInterfaceFactory $dataPageFactory
     * @param ActionCollectionFactory $pageCollectionFactory
     * @param Data\ActionSearchResultsInterfaceFactory $searchResultsFactory
     * @param StoreManagerInterface $storeManager
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ResourceAction $resource,
        ActionFactory $pageFactory,
        Data\ActionInterfaceFactory $dataPageFactory,
        ActionCollectionFactory $pageCollectionFactory,
        Data\ActionSearchResultsInterfaceFactory $searchResultsFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->pageFactory = $pageFactory;
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataPageFactory = $dataPageFactory;
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
        $page = $this->pageFactory->create();
        $page->load($pageId);
        if (!$page->getId()) {
            throw new NoSuchEntityException(__('The Action with the "%1" ID doesn\'t exist.', $pageId));
        }
        return $page;
    }

    /**
     * Delete Page
     *
     * @param ActionInterface $page
     * @return bool
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
        return true;
    }

    /**
     * Delete Page by given Page Identity
     *
     * @param string $pageId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($pageId)
    {
        return $this->delete($this->getById($pageId));
    }
}
