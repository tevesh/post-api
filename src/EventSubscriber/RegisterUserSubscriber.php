<?php


    namespace App\EventSubscriber;


    use ApiPlatform\Core\EventListener\EventPriorities;
    use App\Entity\User;
    use App\Security\TokenGenerator;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpKernel\Event\ViewEvent;
    use Symfony\Component\HttpKernel\KernelEvents;
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

    /**
     * Class RegisterUserSubscriber
     * @package App\EventSubscriber
     */
    class RegisterUserSubscriber implements EventSubscriberInterface
    {
        /** @var UserPasswordEncoderInterface $passwordEncoder */
        private $passwordEncoder;

        /** @var TokenGenerator $tokenGenerator */
        private $tokenGenerator;

        public function __construct(UserPasswordEncoderInterface $passwordEncoder, TokenGenerator $tokenGenerator)
        {
            $this->passwordEncoder = $passwordEncoder;
            $this->tokenGenerator = $tokenGenerator;
        }

        /**
         * @return array|void
         */
        public static function getSubscribedEvents()
        {
            return [
                KernelEvents::VIEW => ['registerUser', EventPriorities::PRE_WRITE],
            ];
        }

        /**
         * @param ViewEvent $event
         */
        public function registerUser(ViewEvent $event): void
        {
            $user = $event->getControllerResult();
            $method = $event->getRequest()->getMethod();
            if (!$user instanceof User || !in_array($method, [Request::METHOD_POST], true)) {
                return;
            }

            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
            $user->setConfirmationToken($this->tokenGenerator->getRandomSecureToken());
        }

    }