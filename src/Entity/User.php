<?php

    namespace App\Entity;

    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\Common\Collections\Collection;
    use Doctrine\ORM\Mapping as ORM;
    use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
    use Symfony\Component\Security\Core\User\UserInterface;
    use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
    use Symfony\Component\Serializer\Annotation\Groups;
    use Symfony\Component\Validator\Constraints as Assert;

    /**
     * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
     * @UniqueEntity("username")
     * @UniqueEntity("email")
     */
    class User implements UserInterface
    {
        public const ROLE_ADMIN = 'ROLE_ADMIN';
        public const ROLE_COMMENTATOR = 'ROLE_COMMENTATOR';
        public const ROLE_EDITOR = 'ROLE_EDITOR';
        public const ROLE_SUPERADMIN = 'ROLE_SUPERADMIN';
        public const ROLE_WRITER = 'ROLE_WRITER';

        public const DEFAULT_ROLES = [self::ROLE_COMMENTATOR];

        /**
         * @ORM\Id()
         * @ORM\GeneratedValue()
         * @ORM\Column(type="integer")
         *
         * @Groups({"get"})
         */
        private $id;

        /**
         * @ORM\Column(type="string", length=255)
         * @Assert\NotBlank(groups={"post"})
         * @Assert\Length(min=6, max=255, groups={"post"})
         *
         * @Groups({"get", "get-blog-post-with-author", "get-comment-with-author", "post"})
         */
        private $username;

        /**
         * @ORM\Column(type="string", length=255)
         * @Assert\NotBlank(groups={"post"})
         * @Assert\Length(min="6", max="255", groups={"post", "put"})
         *
         * @Groups({"get", "get-blog-post-with-author", "get-comment-with-author", "post", "put"})
         */
        private $name;

        /**
         * @ORM\Column(type="string", length=255)
         * @Assert\NotBlank(groups={"post"})
         * @Assert\Regex(
         *     pattern="/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,}/",
         *     message="Password must be eight characters long and contain at leas one digig, one upper case letter and one lowercase letter",
         *     groups={"post"}
         * )
         *
         * @Groups({"post"})
         *
         * @var string $password
         */
        private $password;
        /**
         * @Assert\NotBlank(groups={"post"})
         * @Assert\Expression(
         *     "this.getPassword() === this.getRetypePassword()",
         *      message = "Password does not match",
         *     groups={"post"}
         * )
         *
         * @Groups({"post"})
         *
         * @var string $retypePassword
         */
        private $retypePassword;

        /**
         * @ORM\Column(type="string", length=255)
         * @Assert\NotBlank(groups={"post"})
         * @Assert\Email(groups={"post", "put"})
         * @Assert\Length(min=6, max=255, groups={"post", "put"})
         *
         * @Groups({"get-admin", "get-owner", "post", "put"})
         *
         * @var string $email
         */
        private $email;

        /**
         * @ORM\OneToMany(targetEntity="App\Entity\BlogPost", mappedBy="author")
         * @ORM\JoinColumn(nullable=false)
         *
         * @Groups({"get"})
         * @var  BlogPost[]|ArrayCollection|Collection $comments
         */
        private $blogPosts;

        /**
         * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="author")
         * @ORM\JoinColumn(nullable=false)
         *
         * @Groups({"get"})
         *
         * @var  Comment[]|ArrayCollection|Collection $comments
         */
        private $comments;

        /**
         * @ORM\Column(type="simple_array", length=200, options={"default" : "ROLE_COMMENTATOR"})
         * @Groups({"get-admin", "get-owner"})
         *
         * @var array $roles
         */
        private $roles;

        /**
         * @ORM\Column(type="integer", nullable=true)
         *
         * @var int $passwordChangeDate
         */
        private $passwordChangeDate;
        /**
         * @ORM\Column(type="boolean", options={"default"=false})
         *
         * @var bool $enabled
         */
        private $enabled;

        /**
         * @ORM\Column(type="string", length=40, nullable=true)
         *
         * @var string $confirmationToken
         */
        private $confirmationToken;

        // Virtual methods
        /**
         * @Assert\NotBlank(groups={"put-reset-password"})
         * @Assert\Regex(
         *     pattern="/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,}/",
         *     message="Password must be eight characters long and contain at leas one digig, one upper case letter and one lowercase letter",
         *     groups={"put-reset-password"}
         * )
         *
         * @Groups({"put-reset-password"})
         *
         * @var string $newPassword
         */
        private $newPassword;

        /**
         * @Assert\NotBlank(groups={"put-reset-password"})
         * @Assert\Expression(
         *     "this.getNewPassword() === this.getNewRetypePassword()",
         *     message = "Password does not match",
         *     groups={"put-reset-password"}
         * )
         *
         * @Groups({"put-reset-password"})
         *
         * @var string $newRetypedPassword
         */
        private $newRetypePassword;

        /**
         * @Assert\NotBlank(groups={"put-reset-password"})
         * @UserPassword(groups={"put-reset-password"})
         * @Groups({"put-reset-password"})
         */
        private $oldPassword;

        /**
         * User constructor.
         */
        public function __construct()
        {
            $this->blogPosts = new ArrayCollection();
            $this->comments = new ArrayCollection();
            $this->roles = self::DEFAULT_ROLES;
            $this->enabled = false;
            $this->confirmationToken = null;
        }

        /**
         * @return string
         */
        public function __toString(): string
        {
            return (string) $this->getEmail();
        }

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
        public function getUsername(): ?string
        {
            return $this->username;
        }

        /**
         * @param string $username
         *
         * @return $this
         */
        public function setUsername(string $username): self
        {
            $this->username = $username;

            return $this;
        }

        /**
         * @return string|null
         */
        public function getName(): ?string
        {
            return $this->name;
        }

        /**
         * @param string $name
         *
         * @return $this
         */
        public function setName(string $name): self
        {
            $this->name = $name;

            return $this;
        }

        /**
         * @return string|null
         */
        public function getPassword(): ?string
        {
            return $this->password;
        }

        /**
         * @param string $password
         *
         * @return $this
         */
        public function setPassword(string $password): self
        {
            $this->password = $password;

            return $this;
        }

        /**
         * @return string|null
         */
        public function getEmail(): ?string
        {
            return $this->email;
        }

        /**
         * @param string $email
         *
         * @return $this
         */
        public function setEmail(string $email): self
        {
            $this->email = $email;

            return $this;
        }

        /**
         * @return Collection
         */
        public function getBlogPosts(): Collection
        {
            return $this->blogPosts;
        }

        /**
         * @param Collection $blogPosts
         */
        public function setBlogPosts(Collection $blogPosts): void
        {
            $this->blogPosts = $blogPosts;
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
         */
        public function setComments(Collection $comments): void
        {
            $this->comments = $comments;
        }

        /**
         * @return array
         */
        public function getRoles(): array
        {
            return $this->roles;
        }

        /**
         * @param array $roles
         *
         * @return $this
         */
        public function setRoles(array $roles): self
        {
            $this->roles = $roles;

            return $this;
        }

        /**
         * @return string|null
         */
        public function getSalt(): ?string
        {
            return null;
        }

        /**
         * Not need with authenticated APIs
         */
        public function eraseCredentials(): void
        {
        }

        /**
         * @return string|null
         */
        public function getRetypePassword(): ?string
        {
            return $this->retypePassword;
        }

        /**
         * @param string $retypePassword
         */
        public function setRetypePassword(string $retypePassword): void
        {
            $this->retypePassword = $retypePassword;
        }

        /**
         * @return int|null
         */
        public function getPasswordChangeDate(): ?int
        {
            return $this->passwordChangeDate;
        }

        /**
         * @param int $passwordChangeDate
         */
        public function setPasswordChangeDate(int $passwordChangeDate): void
        {
            $this->passwordChangeDate = $passwordChangeDate;
        }

        /**
         * @return bool
         */
        public function isEnabled(): bool
        {
            return $this->enabled;
        }

        /**
         * @param bool $enabled
         */
        public function setEnabled(bool $enabled): void
        {
            $this->enabled = $enabled;
        }

        /**
         * @return string|null
         */
        public function getConfirmationToken(): ?string
        {
            return $this->confirmationToken;
        }

        /**
         * @param string|null $confirmationToken
         */
        public function setConfirmationToken(?string $confirmationToken): void
        {
            $this->confirmationToken = $confirmationToken;
        }

        /**
         * @return string|null
         */
        public function getNewPassword(): ?string
        {
            return $this->newPassword;
        }

        /**
         * @param string $newPassword
         */
        public function setNewPassword(string $newPassword): void
        {
            $this->newPassword = $newPassword;
        }

        /**
         * @return string|null
         */
        public function getNewRetypePassword(): ?string
        {
            return $this->newRetypePassword;
        }

        /**
         * @param string $newRetypePassword
         */
        public function setNewRetypePassword(string $newRetypePassword): void
        {
            $this->newRetypePassword = $newRetypePassword;
        }

        /**
         * @return string|null
         */
        public function getOldPassword(): ?string
        {
            return $this->oldPassword;
        }

        /**
         * @param string|null $oldPassword
         */
        public function setOldPassword(?string $oldPassword): void
        {
            $this->oldPassword = $oldPassword;
        }

    }
