<?php

    namespace App\EventSubscriber;

    use ApiPlatform\Core\EventListener\EventPriorities;
    use App\Entity\UserConfirmation;
    use App\Repository\UserRepository;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpKernel\Event\ViewEvent;
    use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
    use Symfony\Component\HttpKernel\KernelEvents;

    /**
     * Class UserConfirmationSubscriber
     * @package App\EventSubscriber
     */
    class UserConfirmationSubscriber implements EventSubscriberInterface
    {
        /** @var EntityManagerInterface $entityManager */
        private $entityManager;

        /** @var UserRepository $repository */
        private $repository;

        public function __construct(EntityManagerInterface $entityManager, UserRepository $repository)
        {
            $this->entityManager = $entityManager;
            $this->repository = $repository;
        }

        /**
         * @return array
         */
        public static function getSubscribedEvents(): array
        {
            return [
                KernelEvents::VIEW => ['confirmUser', EventPriorities::POST_VALIDATE],
            ];
        }

        /**
         * @param ViewEvent $event
         */
        public function confirmUser(ViewEvent $event): void
        {
            $request = $event->getRequest();

            // Check if the URL is /api/users/confirm
            if ('api_user_confirmations_post_collection' !== $request->get('_route')) {
                return;
            }

            /** @var UserConfirmation $confirmationToken */
            $confirmationToken = $event->getControllerResult();
            $user = $this->repository->findOneBy(['confirmationToken' => $confirmationToken->confirmationToken]);

            if (!$user) {
                throw new NotFoundHttpException();
            }

            $user->setEnabled(true);
            $user->setConfirmationToken(null);
            $this->entityManager->flush();

            $event->setResponse(new JsonResponse(null, Response::HTTP_OK));

        }

    }