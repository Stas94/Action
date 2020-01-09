<?php
namespace Puga\Action\Model\ResourceModel;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\EntityManager\EntityManager;

class Action extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * @var EntityManager
     * @since 101.0.0
     */
    protected $entityManager;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

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
     * @inheritdoc
     */
    public function delete($object)
    {
        $this->getEntityManager()->delete($object);
        $this->eventManager->dispatch(
            'puga_action_delete_after_done',
            ['product' => $object]
        );
        return $this;
    }

    /**
     * Retrieve entity manager.
     *
     * @return EntityManager
     */
    private function getEntityManager()
    {
        if (null === $this->entityManager) {
            $this->entityManager = ObjectManager::getInstance()
                ->get(EntityManager::class);
        }
        return $this->entityManager;
    }
}
