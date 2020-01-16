<?php
namespace Puga\Action\Model;

use \Magento\Framework\DataObject\IdentityInterface;


/**
 * Cms Page Model
 *
 * @api
 * @method Action setStoreId(int $storeId)
 * @method int getStoreId()
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @since 100.0.2
 */
class ActionProduct extends \Magento\Framework\Model\AbstractModel
{

    protected function _construct()
    {
        $this->_init('Puga\Action\Model\ResourceModel\Product\Action');
    }
}
