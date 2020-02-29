<?php

    namespace App\Service;

    use App\Exception\InvalidConfirmationTokenException;
    use App\Repository\UserRepository;
    use Doctrine\ORM\EntityManagerInterface;

    /**
     * Class UserConfirmationService
     * @package App\Service
     */
    class UserConfirmationService
    {
        /**
         * @var UserRepository
         */
        private $repository;
        /**
         * @var EntityManagerInterface
         */
        private $entityManager;

        /**
         * UserConfirmationService constructor.
         *
         * @param UserRepository $repository
         * @param EntityManagerInterface $entityManager
         */
        public function __construct(UserRepository $repository, EntityManagerInterface $entityManager)
        {
            $this->repository = $repository;
            $this->entityManager = $entityManager;
        }

        /**
         * @param string $confirmationToken
         *
         * @throws InvalidConfirmationTokenException
         */
        public function confirmUser(string $confirmationToken): void
        {
            $user = $this->repository->findOneBy(['confirmationToken' => $confirmationToken]);

            if (!$user) {
                throw new InvalidConfirmationTokenException(sprintf('Cannot find confirmation token %s', $confirmationToken));
            }

            $user->setEnabled(true);
            $user->setConfirmationToken(null);
            $this->entityManager->flush();
        }

    }