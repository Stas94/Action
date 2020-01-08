<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Puga\Action\Api;

use \Puga\Action\Api\Data\ActionInterface;
/**
 * Action CRUD interface.
 * @api
 * @since 100.0.2
 */
interface ActionRepositoryInterface
{
    /**
     * Save page.
     *
     * @param \Puga\Action\Api\Data\ActionInterface $page
     * @return \Puga\Action\Api\Data\ActionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(ActionInterface $page);

    /**
     * Retrieve page.
     *
     * @param int $id
     * @return \Puga\Action\Api\Data\ActionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * Delete page.
     *
     * @param \Puga\Action\Api\Data\ActionInterface $page
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(ActionInterface $page);

    /**
     * Delete page by ID.
     *
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}
