<?php

namespace Puga\Action\Cron;

use Puga\Action\Model\ActionFactory;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Action
{
    /**
     * @var ActionFactory
     */
    private $_actionFactory;

    /**
     * Action constructor.
     * @param ActionFactory $_actionFactory
     */
    public function __construct(ActionFactory $_actionFactory)
    {
        $this->_actionFactory = $_actionFactory;
    }

    /**
     * @return AbstractCollection
     */
    public function execute()
    {
        $actions = $this->_actionFactory->create()->getCollection()
            ->addFieldToFilter('is_active', 1);
        foreach ($actions->getItems() as $actionData) {
            $action = $actionData->getData();
            $startTime = strtotime($actionData->getData('start_datetime'));
            $endTime = strtotime($actionData->getData('end_datetime'));
            $date = strtotime(date('Y-m-d H:i'));
            if ($date >= $startTime) {
            $action['status'] = 2;
            $actions->getResource()->getConnection()->update(
                $actions->getResource()->getTable('puga_action_action'),
                $action,
                $actions->getResource()->getConnection()->quoteInto('id = ?', $action['id']));
            }
            if ($date >= $endTime && !empty($endTime)) {
                $action = $actionData->getData();
                $action['status'] = 3;
                $actions->getResource()->getConnection()->update(
                    $actions->getResource()->getTable('puga_action_action'),
                    $action,
                    $actions->getResource()->getConnection()->quoteInto('id = ?', $action['id']));
            }
        }
        return $actions;
    }
}
