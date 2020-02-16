<?php

    namespace App\EventSubscriber;

    use ApiPlatform\Core\EventListener\EventPriorities;
    use App\Entity\UserConfirmation;
    use App\Service\UserConfirmationService;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpKernel\Event\ViewEvent;
    use Symfony\Component\HttpKernel\KernelEvents;

    /**
     * Class UserConfirmationSubscriber
     * @package App\EventSubscriber
     */
    class UserConfirmationSubscriber implements EventSubscriberInterface
    {
        /**
         * @var UserConfirmationService
         */
        private $userConfirmationService;

        /**
         * UserConfirmationSubscriber constructor.
         *
         * @param UserConfirmationService $userConfirmationService
         */
        public function __construct(UserConfirmationService $userConfirmationService)
        {
            $this->userConfirmationService = $userConfirmationService;
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
            $this->userConfirmationService->confirmUser($confirmationToken->confirmationToken);

            $event->setResponse(new JsonResponse(null, Response::HTTP_OK));

        }

    }