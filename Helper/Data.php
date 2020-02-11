<?php

namespace Puga\Action\Helper;

use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
class Data extends AbstractHelper
{
    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Action constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        StoreManagerInterface $storeManager
    )
    {
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @var $item
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
                'url' => $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'puga/action/image/' . $image
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
}
