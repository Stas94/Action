<?php

namespace Puga\Action\Block\Action\View;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Model\ProductFactory;
use Magento\Store\Model\StoreManagerInterface;

class Action extends AbstractProduct
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Action constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ProductFactory $_productFactory
     * @param  StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ProductFactory $_productFactory,
        \Magento\Framework\Registry $registry,
        StoreManagerInterface $storeManager
    )
    {
        $this->_productFactory = $_productFactory;
        $this->_coreRegistry = $registry;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->_prepareLayout();
    }

    /**
     * Get current action
     *
     * @return null|int
     */
    public function getAction()
    {
        $action = $this->_coreRegistry->registry('puga_action');
        return $action;

    }

    /**
     * @var \Magento\Catalog\Model\ProductFactory $_productFactory
     *
     */
    public function getProductLoad()
    {
        $collectionProduct = $this->_productFactory->create()->getCollection()
            ->addFieldToSelect('*')
            ->joinField(
                'action_id',
                'puga_action_product',
                'action_id',
                'product_id=entity_id',
                'action_id=' . (int)$this->getRequest()->getParam('id')
            );
        return $collectionProduct;
    }

    /**
     * @return AbstractProduct|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $action = $this->getAction();
        $this->setData($action->getData());
    }
}
