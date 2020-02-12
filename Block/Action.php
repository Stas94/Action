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
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5;
        $collection = $this->_actionFactory->create()->getCollection()
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('start_datetime',['lteq' => date('Y-m-d H:i')])
            ->setPageSize($pageSize)
            ->setCurPage($page)
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

    /**
     * Render pagination HTML
     *
     * @return string
     */
    public function getPagerHtml()
    {
        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'action_list_toolbar_pager'
        )->setAvailableLimit([5 => 5, 10 => 10, 15 => 15, 20 => 20])
            ->setShowPerPage(true)->setCollection(
                $this->getActionCollection()
            );
        $this->setChild('pager', $pager);
        $this->getActionCollection()->load();

        return $this->getChildHtml('pager');
    }
}
