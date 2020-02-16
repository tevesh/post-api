<?php

    namespace App\Entity;
    use Doctrine\ORM\Mapping as ORM;
    use Vich\UploaderBundle\Mapping\Annotation as Vich;

    /**
     * Class Image
     * @package App\Entity
     *
     * @ORM\Entity()
     * @Vich\Uploadable()
     */
    class Image
    {
        /**
         * @ORM\Id()
         * @ORM\GeneratedValue()
         * @ORM\Column(type="integer")
         *
         * @var int $id
         */
        private $id;

        /**
         * @ORM\Column(type="string", nullable=true)
         *
         * @var string $url
         */
        private $url;

        // Virtual fields
        /**
         * @Vich\UploadableField(mapping="images", fileNameProperty="url")
         * @var
         */
        private $file;

        /**
         * @return int
         */
        public function getId(): int
        {
            return $this->id;
        }

        /**
         * @return string|null
         */
        public function getUrl(): ?string
        {
            return $this->url;
        }

        /**
         * @param string|null $url
         */
        public function setUrl(?string $url): void
        {
            $this->url = $url;
        }

        /**
         * @return mixed
         */
        public function getFile()
        {
            return $this->file;
        }

        /**
         * @param mixed $file
         */
        public function setFile($file): void
        {
            $this->file = $file;
        }

    }