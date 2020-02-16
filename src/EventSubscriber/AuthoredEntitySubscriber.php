<?php

    declare(strict_types=1);

    namespace App\EventSubscriber;


    use ApiPlatform\Core\EventListener\EventPriorities;
    use App\Entity\AuthorEntityInterface;
    use App\Entity\BlogPost;
    use App\Entity\Comment;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpKernel\Event\ViewEvent;
    use Symfony\Component\HttpKernel\KernelEvents;
    use Symfony\Component\Security\Core\User\UserInterface;
    use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

    /**
     * Class AuthoredEntitySubscriber
     * @package App\EventSubscriber
     */
    class AuthoredEntitySubscriber implements EventSubscriberInterface
    {
        /** TokenStorageInterface $tokenStorage */
        private $tokenStorage;

        public function __construct(TokenStorageInterface $tokenStorage)
        {
            $this->tokenStorage = $tokenStorage;
        }

        /**
         * @return array
         */
        public static function getSubscribedEvents(): array
        {
            return [
                KernelEvents::VIEW => ['getAuthenticateUser', EventPriorities::PRE_WRITE],
            ];
        }

        public function getAuthenticateUser(ViewEvent $event): void
        {
            /** @var BlogPost|Comment $entity */
            $entity = $event->getControllerResult();
            $method = $event->getRequest()->getMethod();

            /** @var UserInterface $author */
            $author = $this->tokenStorage->getToken()->getUser();
            if (!$entity instanceof AuthorEntityInterface || Request::METHOD_POST !== $method) {
                return;
            }

            if (null !== $entity->getAuthor()) {
                return;
            }

            $entity->setAuthor($author);
        }
    }