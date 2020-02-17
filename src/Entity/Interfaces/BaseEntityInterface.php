<?php

    namespace App\Entity\Interfaces;

    use DateTime;

    interface BaseEntityInterface
    {
        /**
         * @return int|null
         */
        public function getId(): ?int;

        /**
         * @return DateTime|null
         */
        public function getCreatedAt(): ?DateTime;

        /**
         * @param DateTime $createdAt
         */
        public function setCreatedAt(DateTime $createdAt): void;

        /**
         * @return DateTime|null
         */
        public function getUpdatedAt(): ?DateTime;

        /**
         * @param DateTime $updatedAt
         */
        public function setUpdatedAt(DateTime $updatedAt): void;

        /**
         * @return string|null
         */
        public function getTimezone(): ?string;

        /**
         * @param string|null $timezone
         */
        public function setTimezone(?string $timezone): void;

    }