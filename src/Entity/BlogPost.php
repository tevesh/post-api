<?php

    namespace App\Entity;

    use App\Entity\Interfaces\AuthorEntityInterface;
    use App\Entity\Interfaces\PublishedDateEntityInterface;
    use DateTimeInterface;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\Common\Collections\Collection;
    use Doctrine\ORM\Mapping as ORM;
    use Exception;
    use Symfony\Component\Security\Core\User\UserInterface;
    use Symfony\Component\Serializer\Annotation\Groups;
    use Symfony\Component\Validator\Constraints as Assert;

    /**
     * @ORM\Entity(repositoryClass="App\Repository\BlogPostRepository")
     */
    class BlogPost extends BaseEntity implements AuthorEntityInterface, PublishedDateEntityInterface
    {
        public const ORDER_ASCENDANT = 'ASC';
        public const ORDER_DESCENDANT = 'DESC';

        /**
         * @ORM\Id()
         * @ORM\GeneratedValue()
         *
         * @ORM\Column(type="integer")
         *
         * @Groups({"get-blog-post-with-author"})
         */
        protected $id;

        /**
         * @ORM\Column(type="string", length=255)
         * @Assert\NotBlank()
         * @Assert\Length(min="10", max="60")
         *
         * @Groups({"get-blog-post-with-author", "post"})
         */
        private $title;

        /**
         * @ORM\Column(type="datetime")
         * @Assert\NotBlank()
         * @Assert\DateTime()
         *
         * @Groups({"get-blog-post-with-author"})
         */
        private $published;

        /**
         * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="blogPosts")
         * @ORM\JoinColumn(nullable=false)
         *
         * @Groups({"get-blog-post-with-author"})
         */
        private $author;

        /**
         * @ORM\Column(type="text")
         * @Assert\NotBlank()
         * @Assert\Length(min="20")
         *
         * @Groups({"get-blog-post-with-author", "post"})
         */
        private $content;

        /**
         * @ORM\Column(type="string", length=255, nullable=true)
         *
         * @Groups({"get-blog-post-with-author", "post"})
         */
        private $slug;

        /**
         * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="blogPost")
         * @ORM\JoinColumn(nullable=false)
         *
         * @Groups({"get-blog-post-with-author"})
         *
         * @var Comment[]|ArrayCollection|Collection $comments
         */
        private $comments;

        /**
         * @ORM\ManyToMany(targetEntity="App\Entity\Image")
         * @ORM\JoinTable()
         *
         * @Groups({"get-blog-post-with-author", "post"})
         *
         * @var  Image[]|ArrayCollection|Collection $images
         */
        private $images;

        /**
         * BlogPost constructor.
         *
         * @throws Exception
         */
        public function __construct()
        {
            BaseEntity::__construct();
            $this->comments = new ArrayCollection();
            $this->images = new ArrayCollection();
        }

        /**
         * @return string|null
         */
        public function getTitle(): ?string
        {
            return $this->title;
        }

        /**
         * @param string $title
         *
         * @return $this
         */
        public function setTitle(string $title): self
        {
            $this->title = $title;
            $slug = str_replace(' ', '-', strtolower($title));
            $this->setSlug($slug);

            return $this;
        }

        /**
         * @return DateTimeInterface|null
         */
        public function getPublished(): ?DateTimeInterface
        {
            return $this->published;
        }

        /**
         * @param DateTimeInterface $published
         *
         * @return $this
         */
        public function setPublished(DateTimeInterface $published): PublishedDateEntityInterface
        {
            $this->published = $published;

            return $this;
        }

        /**
         * @return UserInterface|null
         */
        public function getAuthor(): ?UserInterface
        {
            return $this->author;
        }

        /**
         * @param UserInterface $user
         *
         * @return $this
         */
        public function setAuthor(UserInterface $user): AuthorEntityInterface
        {
            $this->author = $user;

            return $this;
        }

        /**
         * @return string|null
         */
        public function getContent(): ?string
        {
            return $this->content;
        }

        /**
         * @param string $content
         *
         * @return $this
         */
        public function setContent(string $content): self
        {
            $this->content = $content;

            return $this;
        }

        /**
         * @return string|null
         */
        public function getSlug(): ?string
        {
            return $this->slug;
        }

        /**
         * @param string|null $slug
         *
         * @return $this
         */
        public function setSlug(?string $slug): self
        {
            $this->slug = $slug;

            return $this;
        }

        /**
         * @return Collection
         */
        public function getComments(): Collection
        {
            return $this->comments;
        }

        /**
         * @param Collection $comments
         *
         * @return BlogPost
         */
        public function setComments(Collection $comments): self
        {
            $this->comments = $comments;

            return $this;
        }

        /**
         * @return Collection
         */
        public function getImages(): Collection
        {
            return $this->images;
        }

        /**
         * @param Collection $images
         */
        public function setImages(Collection $images): void
        {
            $this->images = $images;
        }

        /**
         * @param Image $image
         */
        public function addImage(Image $image)
        {
            $this->images->add($image);
        }

        /**
         * @param Image $image
         */
        public function removeImage(Image $image)
        {
            $this->images->removeElement($image);
        }

    }