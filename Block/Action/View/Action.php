<?php

namespace Puga\Action\Block\Action\View;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Model\ProductFactory;

class Action extends AbstractProduct
{
    protected $_actionFactory;


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
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Action constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Puga\Action\Model\ActionFactory $_actionFactory
     * @param \Magento\Catalog\Model\ProductFactory $_productFactory
     * @param \Magento\Framework\ObjectManagerInterface
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Puga\Action\Model\ActionFactory $_actionFactory,
        \Magento\Catalog\Model\ProductFactory $_productFactory,
        \Magento\Framework\ObjectManagerInterface $_objectManager,
        StoreManagerInterface $storeManager
    )
    {
        $this->_actionFactory = $_actionFactory;
        $this->_productFactory = $_productFactory;
        $this->_objectManager = $_objectManager;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->_prepareLayout();
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

        $block = $this->getLayout()->createBlock(\Puga\Action\Block\Action::class);
        $actionId = $block->getActionCollection()->getItemById((int)$this->getRequest()->getParam('id'));
        $this->setData($actionId->getData());
    }
}
