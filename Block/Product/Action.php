<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Puga\Action\Block\Product;

use Magento\Framework\View\Element\Template;

/**
 * Product Review Tab
 *
 * @api
 * @author     Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class Action extends Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Action resource model
     *
     * @var \Puga\Action\Model\ResourceModel\Action
     */
    protected $_actionsResource;

    /**
     * Action model
     *
     * @var \Puga\Action\Model\ActionFactory
     */
    protected $_actionsColFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Puga\Action\Model\ResourceModel\Action $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Puga\Action\Model\ResourceModel\Action $_actionsResource,
        \Puga\Action\Model\ActionFactory $_actionsColFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_actionsResource = $_actionsResource;
        $this->_actionsColFactory = $_actionsColFactory;
        parent::__construct($context, $data);

        $this->setTabTitle();
    }

    /**
     * Get current product id
     *
     * @return null|int
     */
    public function getProductId()
    {
        $product = $this->_coreRegistry->registry('product');
        return $product ? $product->getId() : null;
    }

    /**
     * Set tab title
     *
     * @return void
     */
    public function setTabTitle()
    {
        $title = $this->getCollection()
            ? __('Actions %1', '<span class="counter">' . $this->getCollection()->getSize() . '</span>')
            : __('Actions');
        $this->setTitle($title);
    }

    /**
     * Get size of reviews collection
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getCollection()
    {
        $productIds = $this->_actionsResource->getLinkedActions($this->getProductId());
        $collection = $this->_actionsColFactory->create()->getCollection()
            ->addFieldToFilter('is_active', array('eq' => 1))
            ->addFieldToFilter('id', array('in' => $productIds));
        return $collection;
    }
}
