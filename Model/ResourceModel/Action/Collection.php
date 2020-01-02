<?php
namespace Puga\Action\Model\ResourceModel\Action;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'puga_action_action_collection';
    protected $_eventObject = 'action_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Puga\Action\Model\Action', 'Puga\Action\Model\ResourceModel\Action');
    }

}
