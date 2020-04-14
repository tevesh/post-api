<?php
    
    
    namespace App\DataFixtures;
    
    
    use Doctrine\Persistence\ObjectManager;
    use Exception;

    class AppFixtureSilent extends AppFixtures
    {
        /**
         * @param ObjectManager $manager
         *
         * @param bool $silent
         *
         * @throws Exception
         */
        public function load(ObjectManager $manager, bool $silent = true): void
        {
            $this->loadUsers($manager, $silent);
            $this->loadBlogPosts($manager, $silent);
            $this->loadComments($manager, $silent);
        }
    }