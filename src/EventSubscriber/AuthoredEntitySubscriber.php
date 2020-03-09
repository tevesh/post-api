<?php

    declare(strict_types=1);

    namespace App\EventSubscriber;


    use ApiPlatform\Core\EventListener\EventPriorities;
    use App\Entity\BlogPost;
    use App\Entity\Comment;
    use App\Entity\Interfaces\AuthorEntityInterface;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpKernel\Event\ViewEvent;
    use Symfony\Component\HttpKernel\KernelEvents;
    use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
    use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
    use Symfony\Component\Security\Core\User\UserInterface;

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
            
            /** @var TokenInterface $token */
            $token = $this->tokenStorage->getToken();
            if (null === $token) {
                return;
            }
            /** @var UserInterface $author */
            $author = $token->getUser();
            
            if (!$entity instanceof AuthorEntityInterface || Request::METHOD_POST !== $method) {
                return;
            }

            if (null !== $entity->getAuthor()) {
                return;
            }

            $entity->setAuthor($author);
        }
        
    }