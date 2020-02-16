<?php


namespace App\Entity;

use DateTimeInterface;

/**
 * Interface PublishedDateEntityInterface
 * @package App\Entity
 */
interface PublishedDateEntityInterface
{
    /**
     * @return DateTimeInterface|null
     */
    public function getPublished(): ?DateTimeInterface;

    /**
     * @param DateTimeInterface $published
     *
     * @return PublishedDateEntityInterface
     */
    public function setPublished(DateTimeInterface $published): PublishedDateEntityInterface;
}