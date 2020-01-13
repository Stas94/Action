<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Puga\Action\Model\Action;

use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Puga\Action\Model\ResourceModel\Action\Collection;
use Puga\Action\Model\ResourceModel\Action\CollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Framework\AuthorizationInterface;

/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\ModifierPoolDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var AuthorizationInterface
     */
    private $auth;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param StoreManagerInterface $storeManager
     * @param string $requestFieldName
     * @param CollectionFactory $pageCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $pool
     * @param AuthorizationInterface|null $auth
     */
    public function __construct(
        $name,
        $primaryFieldName,
        StoreManagerInterface $storeManager,
        $requestFieldName,
        CollectionFactory $pageCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null,
        ?AuthorizationInterface $auth = null
    ) {
        $this->storeManager = $storeManager;
        $this->collection = $pageCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
        $this->auth = $auth ?? ObjectManager::getInstance()->get(AuthorizationInterface::class);
        $this->meta = $this->prepareMeta($this->meta);
    }

    /**
     * Prepares Meta
     *
     * @param array $meta
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var $page \Puga\Action\Model\Action */
        foreach ($items as $page) {
            $pageData = $page->getData();
            if (array_key_exists('image', $pageData) && $pageData['image']) {
                $image = $pageData['image'];
                $pageData['image'] = [
                    [
                        'name' => $image,
                        'url' => $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'action/image/' . $image
                    ]
                ];
            }
            $this->loadedData[$page->getId()] = $pageData;
        }

        $data = $this->dataPersistor->get('puga_action_action');
        if (!empty($data)) {
            $this->loadedData[$page->getId()] = $data;
            $this->dataPersistor->clear('puga_action_action');
        }

        return $this->loadedData;
    }

    /**
     * @inheritDoc
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        if (!$this->auth->isAllowed('Puga_Action::save')) {
            $designMeta = [
                'design' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'disabled' => true
                            ]
                        ]
                    ]
                ],
                'custom_design_update' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'disabled' => true
                            ]
                        ]
                    ]
                ]
            ];
            $meta = array_merge_recursive($meta, $designMeta);
        }

        return $meta;
    }
}
