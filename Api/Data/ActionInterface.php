<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Puga\Action\Api\Data;

/**
 * Action interface.
 * @api
 * @since 100.0.2
 */
interface ActionInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID                       = 'id';
    const IS_ACTIVE                = 'is_active';
    const NAME                     = 'name';
    const DESCRIPTION              = 'description';
    const SHORT_DESCRIPTION        = 'short_description';
    const IMAGE                    = 'image';
    const CREATION_TIME            = 'creation_datetime';
    const START_TIME               = 'start_datetime';
    const END_TIME                 = 'end_datetime';
    const STATUS                   = 'status';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Is active
     *
     * @return bool|null
     */
    public function getIsActive();

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * Get short description
     *
     * @return string|null
     */
    public function getShortDescription();

    /**
     * Get image
     *
     * @return array|null
     */
    public function getImage();

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreationTime();

    /**
     * Get start time
     *
     * @return string|null
     */
    public function getStartTime();

    /**
     * Get end time
     *
     * @return string|null
     */
    public function getEndTime();

    /**
     * Get status
     *
     * @return string|null
     */
    public function getStatus();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Puga\Action\Api\Data\ActionInterface
     */
    public function setId($id);

    /**
     * Set is active
     *
     * @param int|bool $isActive
     * @return \Puga\Action\Api\Data\ActionInterface
     */
    public function setIsActive($isActive);

    /**
     * Set name
     *
     * @param string $name
     * @return \Puga\Action\Api\Data\ActionInterface
     */
    public function setName($name);

    /**
     * Set description
     *
     * @param string $description
     * @return \Puga\Action\Api\Data\ActionInterface
     */
    public function setDescription($description);

    /**
     * Set short description
     *
     * @param string $shortDescription
     * @return \Puga\Action\Api\Data\ActionInterface
     */
    public function setShortDescription($shortDescription);

    /**
     * Set image
     *
     * @param array $image
     * @return \Puga\Action\Api\Data\ActionInterface
     */
    public function setImage($image);

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return \Puga\Action\Api\Data\ActionInterface
     */
    public function setCreationTime($creationTime);

    /**
     * Set start time
     *
     * @param string $startTime
     * @return \Puga\Action\Api\Data\ActionInterface
     */
    public function setStartTime($startTime);

    /**
     * Set end time
     *
     * @param string $endTime
     * @return \Puga\Action\Api\Data\ActionInterface
     */
    public function setEndTime($endTime);

    /**
     * Set status
     *
     * @param string $status
     * @return \Puga\Action\Api\Data\ActionInterface
     */
    public function setStatus($status);
}
