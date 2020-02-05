<?php
namespace Puga\Action\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Data\Tree\Node;

class MyObserver implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $menu \Magento\Framework\Data\Tree\Node */
        $menu = $observer->getData('menu');
        $tree = $menu->getTree();
        $node = new Node(
          [
              'name' => 'Action',
              'id' => 'puga_action',
              'url' => '/puga_action'
          ],
            'id',
            $tree
        );
        return $node;
    }
}
