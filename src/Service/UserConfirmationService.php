<?php

    namespace App\Service;

    use App\Repository\UserRepository;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
         */
        public function confirmUser(string $confirmationToken): void
        {
            $user = $this->repository->findOneBy(['confirmationToken' => $confirmationToken]);

            if (!$user) {
                throw new NotFoundHttpException();
            }

            $user->setEnabled(true);
            $user->setConfirmationToken(null);
            $this->entityManager->flush();
        }

    }