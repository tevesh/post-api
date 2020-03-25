<?php


namespace App\Entity\Interfaces;

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
     * @param DateTimeInterface|null $published
     *
     * @return PublishedDateEntityInterface
     */
    public function setPublished(?DateTimeInterface $published): PublishedDateEntityInterface;
}