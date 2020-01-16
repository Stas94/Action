<?php
namespace Puga\Action\Model\ResourceModel;

use Magento\Catalog\Model\ResourceModel\Category;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\AbstractModel;

class Action extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Catalog products table name
     *
     * @var string
     */
    protected $_categoryProductTable;

    /**
     * Core event manager proxy
     *
     * @var ManagerInterface
     */
    protected $_eventManager = null;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('puga_action_action', 'id');
    }

    /**
     * Process category data after save category object
     *
     * Save related products ids and update path value
     *
     * @param AbstractModel $object
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->_saveCategoryProducts($object);
        return parent::_afterSave($object);
    }

    /**
     * Save category products relation
     *
     * @param \Puga\Action\Model\Action $action
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _saveCategoryProducts($action)
    {
        $action->setIsChangedProductList(false);
        $id = $action->getId();
        /**
         * new category-product relationships
         */
        $products = $action->getPostedProducts();

        /**
         * Example re-save category
         */
        if ($products === null) {
            return $this;
        }

        /**
         * old category-product relationships
         */
        $oldProducts = $action->getProductsChecked();

        $insert = array_diff_key($products, $oldProducts);
        $delete = array_diff_key($oldProducts, $products);

        /**
         * Find product ids which are presented in both arrays
         * and saved before (check $oldProducts array)
         */
        $update = array_intersect_key($products, $oldProducts);
        $update = array_diff_assoc($update, $oldProducts);

        $connection = $this->getConnection();

        /**
         * Delete products from category
         */
        if (!empty($delete)) {
            $cond = ['product_id IN(?)' => array_keys($delete), 'action_id=?' => $id];
            $connection->delete($this->getCategoryProductTable(), $cond);
        }

        /**
         * Add products to category
         */
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $productId => $position) {
                $data[] = [
                    'action_id' => (int)$id,
                    'product_id' => (int)$productId,
                ];
            }
            $connection->insertMultiple($this->getCategoryProductTable(), $data);
        }
        return $this;
    }


    /**
     * Get positions of associated to category products
     *
     * @param \Puga\Action\Model\Action $action
     * @return array
     */
    public function getProductsChecked($action)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('puga_action_product'),
            ['product_id', 'action_id']
        )->where(
            "{$this->getTable('puga_action_product')}.action_id = ?",
            $action->getId()
        );
        $bind = ['action_id' => (int)$action->getId()];

        return $this->getConnection()->fetchPairs($select, $bind);
    }

    /**
     * Category product table name getter
     *
     * @return string
     */
    public function getCategoryProductTable()
    {
        if (!$this->_categoryProductTable) {
            $this->_categoryProductTable = $this->getTable('puga_action_product');
        }
        return $this->_categoryProductTable;
    }
}
