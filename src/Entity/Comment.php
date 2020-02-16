<?php

    namespace App\Entity;

    use DateTimeInterface;
    use Doctrine\ORM\Mapping as ORM;
    use Symfony\Component\Security\Core\User\UserInterface;
    use Symfony\Component\Serializer\Annotation\Groups;
    use Symfony\Component\Validator\Constraints as Assert;

    /**
     * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
     */
    class Comment implements AuthorEntityInterface, PublishedDateEntityInterface
    {
        /**
         * @ORM\Id()
         * @ORM\GeneratedValue()
         * @ORM\Column(type="integer")
         *
         * @Groups({"get-comment-with-author"})
         */
        private $id;

        /**
         * @ORM\Column(type="text")
         * @Assert\NotBlank()
         * @Assert\Length(min=5, max=3000)
         *
         * @Groups({"get-comment-with-author", "post"})
         */
        private $content;

        /**
         * @ORM\Column(type="datetime")
         *
         * @Groups({"get-comment-with-author"})
         */
        private $published;

        /**
         * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
         * @ORM\JoinColumn(nullable=false)
         *
         * @Groups({"get-comment-with-author"})
         */
        private $author;

        /**
         * @ORM\ManyToOne(targetEntity="App\Entity\BlogPost", inversedBy="comments")
         * @ORM\JoinColumn(nullable=false)
         *
         * @Groups({"post"})
         */
        private $blogPost;

        /**
         * @return int|null
         */
        public function getId(): ?int
        {
            return $this->id;
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
         * @param UserInterface $author
         *
         * @return Comment
         */
        public function setAuthor(UserInterface $author): AuthorEntityInterface
        {
            $this->author = $author;

            return $this;
        }

        /**
         * @return BlogPost|null
         */
        public function getBlogPost(): ?BlogPost
        {
            return $this->blogPost;
        }

        /**
         * @param BlogPost $blogPost
         *
         * @return $this
         */
        public function setBlogPost(BlogPost $blogPost): self
        {
            $this->blogPost = $blogPost;

            return $this;
        }

    }
