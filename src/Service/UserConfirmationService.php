<?php

    namespace App\Service;

    use App\Exception\InvalidConfirmationTokenException;
    use App\Repository\UserRepository;
    use Doctrine\ORM\EntityManagerInterface;
    use Psr\Log\LoggerInterface;

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
         * @var LoggerInterface
         */
        private $logger;

        /**
         * UserConfirmationService constructor.
         *
         * @param UserRepository $repository
         * @param EntityManagerInterface $entityManager
         * @param LoggerInterface $logger
         */
        public function __construct(UserRepository $repository, EntityManagerInterface $entityManager, LoggerInterface $logger)
        {
            $this->repository = $repository;
            $this->entityManager = $entityManager;
            $this->logger = $logger;
        }

        /**
         * @param string $confirmationToken
         *
         * @throws InvalidConfirmationTokenException
         */
        public function confirmUser(string $confirmationToken): void
        {
            $this->logger->debug('Fetching user by confirmation token');
            $user = $this->repository->findOneBy(['confirmationToken' => $confirmationToken]);

            if (!$user) {
                $this->logger->debug('Fetching user by confirmation token not found');
                throw new InvalidConfirmationTokenException(sprintf('Cannot find confirmation token %s', $confirmationToken));
            }

            $user->setEnabled(true);
            $user->setConfirmationToken(null);
            $this->entityManager->flush();

            $this->logger->debug('Confirm user by confirmation token');
        }

    }