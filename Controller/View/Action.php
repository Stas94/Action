<?php
namespace Puga\Action\Controller\View;

use Magento\Framework\Controller\ResultFactory;

class Action extends \Magento\Framework\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Action model
     *
     * @var \Puga\Action\Model\ActionFactory
     */
    protected $_actionsColFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;

    /**
     * Action constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Puga\Action\Model\ActionFactory $_actionsColFactory,
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Registry $registr,
        \Puga\Action\Model\ActionFactory $_actionsColFactory)
    {
        $this->_pageFactory = $pageFactory;
        $this->_coreRegistry = $registr;
        $this->_actionsColFactory = $_actionsColFactory;
        return parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $actionId = $this->getRequest()->getParam('id');
        $viewedActionModel= $this->_actionsColFactory->create()->load($actionId);
        if (!$viewedActionModel->getId()) {
            $this->_forward('noRoute');
        } else {
            $this->_coreRegistry->register('puga_action', $viewedActionModel);
        }
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
