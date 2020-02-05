<?php
namespace Puga\Action\Block;

use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class Action extends \Magento\Framework\View\Element\Template
{
    protected $_actionFactory;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Action constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Puga\Action\Model\ActionFactory $_actionFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Puga\Action\Model\ActionFactory $_actionFactory,
        StoreManagerInterface $storeManager
    )
    {
        $this->_actionFactory = $_actionFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->_prepareLayout();
    }

    /**
     * @return mixed
     */
    public function getActionCollection()
    {
        $collection = $this->_actionFactory->create()->getCollection()
            ->addFieldToFilter('start_datetime',['lteq' => '2020-03.01'])
            ->setOrder('start_datetime');
        return $collection;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImage($item)
    {
        $data = $item->getData();
        if (array_key_exists('image', $data) && $data['image']) {
            $image = $data['image'];
            $pageData['image'] = [
                'name' => $image,
                'url' => $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'action/image/' . $image
            ];
            $data['image'] = $pageData['image'];
        }
        if (is_array($data['image'])) {
            if ($data['image']['url']) {
                return $url = $data['image']['url'];
            }
        }
        return $data['image'];
    }

    /**
     * @param $date
     */
    public function getDate($date)
    {
        $timestampEnd = strtotime($date['end_datetime']);
        $start = date('d');
        $end = date('d', $timestampEnd);

        $result = $end - $start;
        return $result;
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return parent::getUrl($route, $params);
    }

}
