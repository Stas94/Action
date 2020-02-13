<?php
namespace Puga\Action\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\UrlInterface;
use \Magento\Store\Model\StoreManagerInterface;

class MyObserver implements ObserverInterface
{

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var UrlInterface
     */
    protected $_urlInterface;

    /**
     * MyObserver constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $_storeManager
     * @param \Magento\Framework\UrlInterface $urlInterface
     */
    public function __construct(
        StoreManagerInterface $_storeManager,
        UrlInterface $urlInterface
    )
    {
        $this->_storeManager = $_storeManager;
        $this->_urlInterface = $urlInterface;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $menu \Magento\Framework\Data\Tree\Node */
        $menu = $observer->getData('menu');
        $tree = $menu->getTree();
        $node = new Node(
          [
              'name' => __('Action'),
              'id' => 'puga_action',
              'url' => $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB) . 'puga_action/index/action/'
          ],
            'id',
            $tree,
            $menu

        );
        return $menu->addChild($node);
    }
}
