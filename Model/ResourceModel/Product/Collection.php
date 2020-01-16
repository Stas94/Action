<?php
namespace Puga\Action\Model\ResourceModel\Product;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'puga_action_product_collection';
    protected $_eventObject = 'action_product_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Puga\Action\Model\ActionProduct', 'Puga\Action\Model\ResourceModel\ActionProduct');
    }

}
