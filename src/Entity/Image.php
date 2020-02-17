<?php

    namespace App\Entity;
    use AppBundle\Entity\Base\BaseEntity;
    use Doctrine\ORM\Mapping as ORM;
    use Symfony\Component\Validator\Constraints as Assert;
    use Vich\UploaderBundle\Mapping\Annotation as Vich;

    /**
     * @ORM\Entity()
     * @Vich\Uploadable()
     */
    class Image extends BaseEntity
    {
        /**
         * @ORM\Column(type="string", nullable=true)
         *
         * @var string $url
         */
        private $url;

        // Virtual fields

        /**
         * @Vich\UploadableField(mapping="images", fileNameProperty="url")
         * @Assert\NotNull()
         */
        private $file;

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