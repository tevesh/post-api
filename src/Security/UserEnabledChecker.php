<?php

    namespace App\Security;

    use App\Entity\User;
    use Symfony\Component\Security\Core\Exception\DisabledException;
    use Symfony\Component\Security\Core\User\UserCheckerInterface;
    use Symfony\Component\Security\Core\User\UserInterface;

    class UserEnabledChecker implements UserCheckerInterface
    {
        /**
         * Checks the user account before authentication.
         *
         * @param UserInterface $user
         */
        public function checkPreAuth(UserInterface $user): void
        {
            if (!$user instanceof User) {
                return;
            }

            if (!$user->isEnabled()) {
                throw new DisabledException();
            }
        }

        /**
         * Checks the user account after authentication.
         *
         * @param UserInterface $user
         */
        public function checkPostAuth(UserInterface $user)
        {
            // TODO: Implement checkPostAuth() method.
        }

    }