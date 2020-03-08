<?php

    namespace App\Controller;

    use App\Entity\User;
    use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController as BaseAdminController;
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

    class UserAdminController extends BaseAdminController
    {
        /**
         * @var UserPasswordEncoderInterface
         */
        private $passwordEncoder;

        /**
         * UserAdminController constructor.
         *
         * @param UserPasswordEncoderInterface $passwordEncoder
         */
        public function __construct(UserPasswordEncoderInterface $passwordEncoder)
        {
            $this->passwordEncoder = $passwordEncoder;
        }

        /**
         * @param User $user
         */
        private function encodeUserPassword(User $user): void
        {
            if ($this->passwordEncoder->needsRehash($user)) {
                $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
            }
        }

        /**
         * Allows applications to modify the entity associated with the item being
         * created while persisting it.
         *
         * @param User $entity
         */
        protected function persistEntity($entity)
        {
            $this->encodeUserPassword($entity);
            parent::persistEntity($entity);
        }

        /**
         * Allows applications to modify the entity associated with the item being
         * edited before updating it.
         *
         * @param User $entity
         */
        protected function updateEntity($entity)
        {
            $this->encodeUserPassword($entity);
            parent::updateEntity($entity);
        }



    }