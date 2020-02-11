<?php
namespace Puga\Action\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Puga\Action\Model\ActionFactory;

class Action extends Template
{
    /**
     * @var ActionFactory
     */
    protected $_actionFactory;

    /**
     * Action constructor.
     * @param Context $context
     * @param ActionFactory $_actionFactory
     */
    public function __construct(
        Context $context,
        ActionFactory $_actionFactory
    )
    {
        $this->_actionFactory = $_actionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->_prepareLayout();
    }

    /**
     * @return mixed
     */
    public function getActionCollection()
    {
        $collection = $this->_actionFactory->create()->getCollection()
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('start_datetime',['lteq' => date('Y-m-d H:i')])
            ->setOrder('start_datetime');
        return $collection;
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return parent::getUrl($route, $params);
    }

}
