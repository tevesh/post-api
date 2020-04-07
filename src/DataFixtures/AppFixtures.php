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
         * @return array
         */
        protected function getRandomRole(): array
        {
            try {
                $role = self::USER_ROLES[random_int(0, count(self::USER_ROLES))];
            } catch (Exception $exception) {
                printf("Catch exception: {$exception->getMessage()}\n\n");

                return $this->getRandomRole();
            }

            if (empty($role)) {
                return $this->getRandomRole();
            }
            printf("return role: {$role}\n\n");

            return [$role];
        }

        /**
         * @param ObjectManager $manager
         *
         * @return BlogPost
         */
        protected function getRandomBlogPost(ObjectManager $manager): BlogPost
        {
            $min = min($this->blogPostsIds);
            printf("Min blog id is {$min}\n");
            $max = max($this->blogPostsIds);
            printf("Max blog id is {$max}\n");
            try {
                $blogPost = $manager->getRepository('App:BlogPost')->find(random_int($min, $max));
            } catch (Exception $exception) {
                printf("Catch exception {$exception->getMessage()}\n\n");

                return $this->getRandomBlogPost($manager);
            }

            if (empty($blogPost)) {
                return $this->getRandomBlogPost($manager);
            }
            printf("Return blog {$blogPost->getId()}\n\n");

            return $blogPost;
        }

        /**
         * @param ObjectManager $manager
         *
         * @param string $role
         *
         * @return User
         */
        protected function getRandomUserByRole(ObjectManager $manager, string $role): User
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
                printf("Catch exception {$exception->getMessage()}\n\n");

                return $this->getRandomUserByRole($manager, $role);
            }
            if (null === $user) {
                return $this->getRandomUserByRole($manager, $role);
            }
            printf("Get user {$user->getUsername()}\n\n");

            return $user;
        }

        /**
         * @param ObjectManager $manager
         *
         * @throws Exception
         */
        public function loadBlogPosts(ObjectManager $manager): void
        {
            for ($i = 0; $i < 100; $i++) {
                // Setup a new fake entity
                $title = $this->faker->text(60);
                printf("Create blog post {$i}\n");
                $blogPost = new BlogPost();
                printf("Set title for blog post {$i}\n");
                $blogPost->setTitle($title);
                printf("Set publish date for blog post {$i}\n");
                $blogPost->setPublished($this->faker->dateTimeThisYear);
                printf("Set content for blog post {$i}\n");
                $blogPost->setContent($this->faker->realText());
                printf("Set author for blog post {$i}\n");
                $blogPost->setAuthor($this->getRandomUserByRole($manager, User::ROLE_EDITOR));
                printf("Set slug for blog post {$i}\n");
                $blogPost->setSlug($this->textManipulation->slugify($title));
                printf("Persist blog post {$i}\n\n");
                $manager->persist($blogPost);
                // Persist ALL operations pending inside manager
                printf("Flush entity manager for all blog posts\n");
                $manager->flush();
                $manager->clear();
                $this->blogPostsIds[] = $blogPost->getId();
            }
        }

        /**
         * @param ObjectManager $manager
         *
         * @throws Exception
         */
        public function loadComments(ObjectManager $manager): void
        {
            for ($i = 0; $i < 10; $i++) {
                for ($j = 0; $j <= 10; $j++) {
                    // Setup a new fake entity
                    printf("Create comment {$i}:{$j}\n");
                    $comment = new Comment();
                    printf("Set comment content {$i}:{$j}\n");
                    $comment->setContent($this->faker->text());
                    printf("Set comment publish date {$i}:{$j}\n");
                    $comment->setPublished($this->faker->dateTimeThisYear);
                    printf("Set comment author {$i}:{$j}\n");
                    $comment->setAuthor($this->getRandomUserByRole($manager, User::ROLE_COMMENTATOR));
                    printf("Set comment blog post {$i}:{$j}\n");
                    $comment->setBlogPost($this->getRandomBlogPost($manager));
                    printf("Persist comment {$i}:{$j}\n\n");
                    $manager->persist($comment);
                }
            }
            // Persist ALL operations pending inside manager
            printf("Flush entity manager for all comments\n");
            $manager->flush();
        }

        /**
         * @param ObjectManager $manager
         *
         * @throws Exception
         */
        public function loadUsers(ObjectManager $manager): void
        {
            echo sprintf("Create an admin user\n");
            $adminUser = new User();
            $adminUser->setName('admin');
            $adminUser->setEmail('admin@post-api.dev.it');
            $adminUser->setUsername('admin');
            $adminUser->setPassword($this->passwordEncoder->encodePassword($adminUser, 'Test123!'));
            $adminUser->setRoles([User::ROLE_SUPERADMIN]);
            $adminUser->setEnabled(true);
            $manager->persist($adminUser);
            
            echo sprintf("Create all other users\n");
            for ($i = 0; $i < 10; $i++) {
                printf("Create user {$i}\n");
                // Setup a new fake entity
                $user = new User();
                printf("Set name for user {$i}\n");
                $user->setName($this->faker->name);
                printf("Set username for user {$i}\n");
                $user->setUsername($this->faker->userName);
                printf("Set email for user {$i}\n");
                $user->setEmail($this->faker->email);
                printf("Set password for user {$i}\n");
                $user->setPassword($this->passwordEncoder->encodePassword($user, 'Test123!'));
                printf("Set roles for user {$i}\n");
                $user->setRoles($this->getRandomRole());
                printf("Set user enabled {$i}\n");
                $user->setEnabled((bool)random_int(0, 1));
                if (!$user->isEnabled()) {
                    printf("Set confirmation token {$i}\n");
                    $user->setConfirmationToken($this->tokenGenerator->getRandomSecureToken());
                }
                printf("Persist user {$i}\n");
                $manager->persist($user);
            }
            // Persist ALL operations pending inside manager
            printf("Flush entity manager for all users\n");
            $manager->flush();
        }

        /**
         * @param ObjectManager $manager
         *
         * @throws Exception
         */
        public function load(ObjectManager $manager): void
        {
            $this->loadUsers($manager);
            $this->loadBlogPosts($manager);
            $this->loadComments($manager);
        }

    }
