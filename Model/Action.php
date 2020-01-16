<?php
namespace Puga\Action\Model;

use \Magento\Framework\DataObject\IdentityInterface;
use Puga\Action\Api\Data\ActionInterface;


/**
 * Cms Page Model
 *
 * @api
 * @method Action setStoreId(int $storeId)
 * @method int getStoreId()
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @since 100.0.2
 */
class Action extends \Magento\Framework\Model\AbstractModel implements ActionInterface, IdentityInterface
{
    /**#@+
     * Page's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    /**#@-*/

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

    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ID);
    }


    /**
     * Get is_active
     *
     * @return string
     */
    public function getIsActive()
    {
        return (bool)$this->getData(self::IS_ACTIVE);
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Get description
     *
     * @return  string
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * Get short description
     *
     * @return  string
     */
    public function getShortDescription()
    {
        return $this->getData(self::SHORT_DESCRIPTION);
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        $this->getData(self::IMAGE);
    }

    /**
     * Get create time
     *
     * @return string
     */
    public function getCreationTime()
    {
        $this->getData(self::CREATION_TIME);
    }

    /**
     * Get start time
     *
     * @return string
     */
    public function getStartTime()
    {
        $this->getData(self::START_TIME);
    }

    /**
     * Get end time
     *
     * @return string
     */
    public function getEndTime()
    {
        $this->getData(self::END_TIME);
    }

    /**
     * status
     *
     * @param string
     */
    public function getStatus()
    {
        $this->getData(self::STATUS);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return \Puga\Action\Api\Data\ActionInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }


    /**
     * Set is_active
     *
     * @param int|bool $isActive
     * @return \Puga\Action\Api\Data\ActionInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * Set name
     *
     * @param string $name
     * @return \Puga\Action\Api\Data\ActionInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Set description
     *
     * @param string $description
     * @return \Puga\Action\Api\Data\ActionInterface
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Set short description
     *
     * @param string $shortDescription
     * @return \Puga\Action\Api\Data\ActionInterface
     */
    public function setShortDescription($shortDescription)
    {
        return $this->setData(self::SHORT_DESCRIPTION, $shortDescription);
    }

    /**
     * Set image
     *
     * @return string $image
     * @return \Puga\Action\Api\Data\ActionInterface
     */
    public function setImage($image)
    {
        $this->setData(self::IMAGE, $image);
    }

    /**
     * Set create time
     *
     * @param string $creationTime
     * @return \Puga\Action\Api\Data\ActionInterface
     */
    public function setCreationTime($creationTime)
    {
        $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * Set start time
     *
     * @param string $startTime
     * @return \Puga\Action\Api\Data\ActionInterface
     */
    public function setStartTime($startTime)
    {
        $this->setData(self::START_TIME, $startTime);
    }

    /**
     * Set end time
     *
     * @param string $endTime
     * @return \Puga\Action\Api\Data\ActionInterface
     */
    public function setEndTime($endTime)
    {
        $this->setData(self::END_TIME, $endTime);
    }

    /**
     * Set status
     *
     * @param string $status
     * @return \Puga\Action\Api\Data\ActionInterface
     */
    public function setStatus($status)
    {
        $this->setData(self::STATUS, $status);
    }

    /**
     * Retrieve array of product id's for category
     *
     * The array returned has the following format:
     *
     * @return array
     */
    public function getProductsChecked()
    {
        if (!$this->getId()) {
            return [];
        }

        $array = $this->getData('products_position');
        if ($array === null) {
            $array = $this->getResource()->getProductsChecked($this);
            $this->setData('products_position', $array);
        }
        return $array;
    }
}
