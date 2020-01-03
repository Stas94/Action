<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Puga\Action\Block\Adminhtml\Action\Edit;

use Magento\Backend\Block\Widget\Context;
use Puga\Action\Model\Action;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GenericButton
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Action
     */
    protected $blockRepository;

    /**
     * @param Context $context
     * @param Action $blockRepository
     */
    public function __construct(
        Context $context,
        Action $blockRepository
    ) {
        $this->context = $context;
        $this->blockRepository = $blockRepository;
    }

    /**
     * Return Action ID
     *
     * @return int|null
     */
    public function getActionId()
    {
        try {
            return $this->blockRepository->load(
                $this->context->getRequest()->getParam('id')
            )->getId();
        } catch (NoSuchEntityException $e) {
        }
        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
