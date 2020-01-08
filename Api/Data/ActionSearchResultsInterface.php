<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Puga\Action\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for cms page search results.
 * @api
 * @since 100.0.2
 */
interface ActionSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get pages list.
     *
     * @return \Puga\Action\Api\Data\ActionInterface[]
     */
    public function getItems();

    /**
     * Set pages list.
     *
     * @param \Puga\Action\Api\Data\ActionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
