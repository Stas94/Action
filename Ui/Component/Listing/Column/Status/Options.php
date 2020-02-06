<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Puga\Action\Ui\Component\Listing\Column\Status;

/**
 * @api
 * @since 100.0.2
 */
class Options implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 1, 'label' => __('The time has not yet come')], ['value' => 2, 'label' => __('Action in progress')],
            ['value' => 3, 'label' => __('Action closed')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [1 => __('The time has not yet come'), 2 => __('Action in progress'), 3 =>__('Action closed')];
    }
}
