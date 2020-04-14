<?php

    namespace App\DataFixtures;

    use App\Entity\BlogPost;
    use App\Entity\Comment;
    use App\Entity\User;
    use App\Security\TokenGenerator;
    use App\Service\TextManipulationService;
    use Doctrine\Bundle\FixturesBundle\Fixture;
    use Doctrine\Persistence\ObjectManager;
    use Exception;
    use Faker\Factory;
    use Faker\Generator;
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

    /**
     * Class AppFixtures
     * @package App\DataFixtures
     */
    class AppFixtures extends Fixture
    {
        protected const USER_ROLES = [
            User::ROLE_COMMENTATOR,
            User::ROLE_EDITOR,
            User::ROLE_WRITER,
        ];

        protected $blogPostsIds = [];

        /** @var UserPasswordEncoderInterface $passwordEncoder */
        private $passwordEncoder;

        /** @var Generator $faker */
        private $faker;

        /** @var TextManipulationService $textManipulation */
        private $textManipulation;

        /** @var TokenGenerator $tokenGenerator */
        private $tokenGenerator;

        /**
         * AppFixtures constructor.
         *
         * @param UserPasswordEncoderInterface $passwordEncoder
         * @param TextManipulationService $textManipulation
         * @param TokenGenerator $tokenGenerator
         */
        public function __construct(UserPasswordEncoderInterface $passwordEncoder, TextManipulationService $textManipulation, TokenGenerator $tokenGenerator)
        {
            $this->passwordEncoder = $passwordEncoder;
            $this->faker = Factory::create();
            $this->textManipulation = $textManipulation;
            $this->tokenGenerator = $tokenGenerator;
        }
    
        /**
         * @param bool $silent
         *
         * @return array
         */
        protected function getRandomRole(bool $silent): array
        {
            try {
                $role = self::USER_ROLES[random_int(0, count(self::USER_ROLES) -1)];
            } catch (Exception $exception) {
                $silent ?: printf("Catch exception: {$exception->getMessage()}\n\n");

                return $this->getRandomRole($silent);
            }

            if (empty($role)) {
                return $this->getRandomRole($silent);
            }
            $silent ?: printf("return role: {$role}\n\n");

            return [$role];
        }
    
        /**
         * @param ObjectManager $manager
         *
         * @param bool $silent
         *
         * @return BlogPost
         */
        protected function getRandomBlogPost(ObjectManager $manager, bool $silent): BlogPost
        {
            $min = min($this->blogPostsIds);
            $silent ?: printf("Min blog id is {$min}\n");
            $max = max($this->blogPostsIds);
            $silent ?: printf("Max blog id is {$max}\n");
            try {
                $blogPost = $manager->getRepository('App:BlogPost')->find(random_int($min, $max));
            } catch (Exception $exception) {
                $silent ?: printf("Catch exception {$exception->getMessage()}\n\n");

                return $this->getRandomBlogPost($manager, $silent);
            }

            if (empty($blogPost)) {
                return $this->getRandomBlogPost($manager, $silent);
            }
            $silent ?: printf("Return blog {$blogPost->getId()}\n\n");

            return $blogPost;
        }
    
        /**
         * @param ObjectManager $manager
         *
         * @param string $role
         *
         * @param bool $silent
         *
         * @return User
         */
        protected function getRandomUserByRole(ObjectManager $manager, string $role, bool $silent): User
        {
            $usersWithRoleEditor = $manager->getRepository('App:User')->findByRole($role);
            try {
                /** @var User $user */
                if (is_array($usersWithRoleEditor)) {
                    $user = $usersWithRoleEditor[array_rand($usersWithRoleEditor)];
                } else {
                    $user = $usersWithRoleEditor;
                }
            } catch (Exception $exception) {
                $silent ?: printf("Catch exception {$exception->getMessage()}\n\n");

                return $this->getRandomUserByRole($manager, $role, $silent);
            }
            if (null === $user) {
                return $this->getRandomUserByRole($manager, $role, $silent);
            }
            $silent ?: printf("Get user {$user->getUsername()}\n\n");

            return $user;
        }
    
        /**
         * @param ObjectManager $manager
         *
         * @param bool $silent
         *
         * @throws Exception
         */
        public function loadBlogPosts(ObjectManager $manager, bool $silent): void
        {
            for ($i = 0; $i < 100; $i++) {
                // Setup a new fake entity
                $title = $this->faker->text(60);
                $silent ?: printf("Create blog post {$i}\n");
                $blogPost = new BlogPost();
                $silent ?: printf("Set title for blog post {$i}\n");
                $blogPost->setTitle($title);
                $silent ?: printf("Set publish date for blog post {$i}\n");
                $blogPost->setPublished($this->faker->dateTimeThisYear);
                $silent ?: printf("Set content for blog post {$i}\n");
                $blogPost->setContent($this->faker->realText());
                $silent ?: printf("Set author for blog post {$i}\n");
                $blogPost->setAuthor($this->getRandomUserByRole($manager, User::ROLE_EDITOR, $silent));
                $silent ?: printf("Set slug for blog post {$i}\n");
                $blogPost->setSlug($this->textManipulation->slugify($title));
                $silent ?: printf("Persist blog post {$i}\n\n");
                $manager->persist($blogPost);
                // Persist ALL operations pending inside manager
                $silent ?: printf("Flush entity manager for all blog posts\n");
                $manager->flush();
                $manager->clear();
                $this->blogPostsIds[] = $blogPost->getId();
            }
        }
    
        /**
         * @param ObjectManager $manager
         *
         * @param bool $silent
         *
         * @throws Exception
         */
        public function loadComments(ObjectManager $manager, bool $silent): void
        {
            for ($i = 0; $i < 10; $i++) {
                for ($j = 0; $j <= 10; $j++) {
                    // Setup a new fake entity
                    $silent ?: printf("Create comment {$i}:{$j}\n");
                    $comment = new Comment();
                    $silent ?: printf("Set comment content {$i}:{$j}\n");
                    $comment->setContent($this->faker->text());
                    $silent ?: printf("Set comment publish date {$i}:{$j}\n");
                    $comment->setPublished($this->faker->dateTimeThisYear);
                    $silent ?: printf("Set comment author {$i}:{$j}\n");
                    $comment->setAuthor($this->getRandomUserByRole($manager, User::ROLE_COMMENTATOR, $silent));
                    $silent ?: printf("Set comment blog post {$i}:{$j}\n");
                    $comment->setBlogPost($this->getRandomBlogPost($manager, $silent));
                    $silent ?: printf("Persist comment {$i}:{$j}\n\n");
                    $manager->persist($comment);
                }
            }
            // Persist ALL operations pending inside manager
            $silent ?: printf("Flush entity manager for all comments\n");
            $manager->flush();
        }
    
        /**
         * @param ObjectManager $manager
         *
         * @param bool $silent
         *
         * @throws Exception
         */
        public function loadUsers(ObjectManager $manager, bool $silent): void
        {
            $silent ?: printf("Create base users\n");
            
            // Create a static superadmin user
            $adminUser = new User();
            $adminUser->setName('admin');
            $adminUser->setEmail('admin@post-api.dev.it');
            $adminUser->setUsername('admin');
            $adminUser->setPassword($this->passwordEncoder->encodePassword($adminUser, 'Test123!'));
            $adminUser->setRoles([User::ROLE_SUPERADMIN]);
            $adminUser->setEnabled(true);
            $manager->persist($adminUser);
    
            // Create a static base users to be sure that exist at least one per kind
            foreach (self::USER_ROLES as $userRole) {
                $baseUser = new User();
                $baseUser->setName($this->faker->name);
                $baseUser->setEmail($this->faker->email);
                $baseUser->setUsername($this->faker->userName);
                $baseUser->setPassword($this->passwordEncoder->encodePassword($baseUser, 'Test123!'));
                $baseUser->setRoles([$userRole]);
                $baseUser->setEnabled(true);
                $manager->persist($baseUser);
            }
            
            $silent ?: printf("Create all other users\n");
            
            for ($i = 0; $i < 10; $i++) {
                $silent ?: printf("Create user {$i}\n");
                // Setup a new fake entity
                $user = new User();
                $silent ?: printf("Set name for user {$i}\n");
                $user->setName($this->faker->name);
                $silent ?: printf("Set username for user {$i}\n");
                $user->setUsername($this->faker->userName);
                $silent ?: printf("Set email for user {$i}\n");
                $user->setEmail($this->faker->email);
                $silent ?: printf("Set password for user {$i}\n");
                $user->setPassword($this->passwordEncoder->encodePassword($user, 'Test123!'));
                $silent ?: printf("Set roles for user {$i}\n");
                $user->setRoles($this->getRandomRole($silent));
                $silent ?: printf("Set user enabled {$i}\n");
                $user->setEnabled((bool)random_int(0, 1));
                if (!$user->isEnabled()) {
                    $silent ?: printf("Set confirmation token {$i}\n");
                    $user->setConfirmationToken($this->tokenGenerator->getRandomSecureToken());
                }
                $silent ?: printf("Persist user {$i}\n");
                $manager->persist($user);
            }
            // Persist ALL operations pending inside manager
            $silent ?: printf("Flush entity manager for all users\n");
            $manager->flush();
        }
    
        /**
         * @param ObjectManager $manager
         *
         * @param bool $silent
         *
         * @throws Exception
         */
        public function load(ObjectManager $manager, bool $silent = false): void
        {
            $this->loadUsers($manager, $silent);
            $this->loadBlogPosts($manager, $silent);
            $this->loadComments($manager, $silent);
        }

    }
