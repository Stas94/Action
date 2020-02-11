<?php
namespace Puga\Action\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;

class Action extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Catalog products table name
     *
     * @var string
     */
    protected $_actionProductTable;


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
     *
     * @param AbstractModel $object
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->_saveActionProducts($object);
        return parent::_afterSave($object);
    }

    /**
     * Save action products relation
     *
     * @param \Puga\Action\Model\Action $action
     * @return $this
     */
    protected function _saveActionProducts($action)
    {
        $action->setIsChangedProductList(false);
        $actionId = $action->getId();

        $products = $action->getPostedProducts();

        if ($products === null) {
            return $this;
        }

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
            $cond = ['product_id IN(?)' => array_keys($delete), 'action_id=?' => $actionId];
            $connection->delete($this->getActionProductTable(), $cond);
        }

        /**
         * Add products to action
         */
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $productId => $position) {
                $data[] = [
                    'action_id' => (int)$actionId,
                    'product_id' => (int)$productId,
                ];
            }
            $connection->insertMultiple($this->getActionProductTable(), $data);
        }
        return $this;
    }


    /**
     * Get positions of associated to action products
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
     * Акції які прив'язані то товару
     *
     * @return array
     */
    public function getLinkedActions($productId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('puga_action_product'),
            array('action_id', 'action_id')
        )->where(
            "{$this->getTable('puga_action_product')}.product_id = ?",
            $productId
        );
        $bind = array('product_id' => (int)$productId);

        return $this->getConnection()->fetchPairs($select, $bind);
    }

    /**
     * Action product table name getter
     *
     * @return string
     */
    public function getActionProductTable()
    {
        if (!$this->_actionProductTable) {
            $this->_actionProductTable = $this->getTable('puga_action_product');
        }
        return $this->_actionProductTable;
    }
}
