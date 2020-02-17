<?php

    namespace App\Entity;

    use App\Entity\Interfaces\BaseEntityInterface;
    use DateTime;
    use Doctrine\ORM\Mapping as ORM;
    use Exception;

    /**
     * @ORM\MappedSuperclass
     * @ORM\HasLifecycleCallbacks
     */
    class BaseEntity implements BaseEntityInterface
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue(strategy="AUTO")
         *
         * @var int $id
         */
        protected $id;

        /**
         * @ORM\Column(type="datetime", name="created_at", nullable=true)
         *
         * @var DateTime $createdAt
         */
        protected $createdAt;

        /**
         * @ORM\Column(type="datetime", name="update_at", nullable=true)
         *
         * @var DateTime $updatedAt
         */
        protected $updatedAt;

        /**
         * @ORM\Column(type="string", name="timezone", nullable=true)
         *
         * @var string $timezone
         */
        protected $timezone;

        /**
         * BaseEntity constructor.
         *
         * @throws Exception
         */
        public function __construct()
        {
            $dateNow = new DateTime('now');
            $this->setCreatedAt($dateNow);
            $this->setUpdatedAt($dateNow);
            $this->timezone = $dateNow->getTimezone()->getName();
        }

        /**
         * @ORM\PrePersist
         * @ORM\PreUpdate
         */
        public function updatedTimestamps(): void
        {
            $dateTimeNow = new DateTime('now');
            $this->setUpdatedAt($dateTimeNow);
            if ($this->getCreatedAt() === null) {
                $this->setCreatedAt($dateTimeNow);
            }
        }

        /**
         * @return int|null
         */
        public function getId(): ?int
        {
            return $this->id;
        }

        /**
         * @return DateTime|null
         */
        public function getCreatedAt(): ?DateTime
        {
            return $this->createdAt;
        }

        /**
         * @param DateTime $createdAt
         */
        public function setCreatedAt(DateTime $createdAt): void
        {
            $this->createdAt = $createdAt;
        }

        /**
         * @return DateTime|null
         */
        public function getUpdatedAt(): ?DateTime
        {
            return $this->updatedAt;
        }

        /**
         * @param DateTime $updatedAt
         */
        public function setUpdatedAt(DateTime $updatedAt): void
        {
            $this->updatedAt = $updatedAt;
        }

        /**
         * @return string|null
         */
        public function getTimezone(): ?string
        {
            return $this->timezone;
        }

        /**
         * @param string|null $timezone
         */
        public function setTimezone(?string $timezone): void
        {
            $this->timezone = $timezone;
        }

    }