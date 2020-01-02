<?php
namespace Puga\Action\Model;
class Action extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'puga_action_action';

    protected $_cacheTag = 'puga_action_action';

    protected $_eventPrefix = 'puga_action_action';

    protected function _construct()
    {
        $this->_init('Puga\Action\Model\ResourceModel\Action');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}
