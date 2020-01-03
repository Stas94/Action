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
class Action extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
    /**
     * No route page id
     */
    const NOROUTE_PAGE_ID = 'no-route';

    const CACHE_TAG = 'puga_action_action';

    protected $_cacheTag = 'puga_action_action';

    protected $_eventPrefix = 'puga_action_action';

    protected function _construct()
    {
        $this->_init('Puga\Action\Model\ResourceModel\Action');
    }

    /**
     * Load object data
     *
     * @param int|null $id
     * @param string $field
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRoutePage();
        }
        return parent::load($id, $field);
    }

    /**
     * Load No-Route Page
     *
     * @return \Puga\Action\Model\Action
     */
    public function noRoutePage()
    {
        return $this->load(self::NOROUTE_PAGE_ID, $this->getIdFieldName());
    }

    /**
     * Get identities
     *
     * @return array
     */
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
