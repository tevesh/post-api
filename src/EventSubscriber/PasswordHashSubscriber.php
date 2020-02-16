<?php


    namespace App\EventSubscriber;


    use ApiPlatform\Core\EventListener\EventPriorities;
    use App\Entity\User;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpKernel\Event\ViewEvent;
    use Symfony\Component\HttpKernel\KernelEvents;
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

    /**
     * Class PasswordHashSubscriber
     * @package App\EventSubscriber
     */
    class PasswordHashSubscriber implements EventSubscriberInterface
    {
        /** @var UserPasswordEncoderInterface $passwordEncoder */
        private $passwordEncoder;

        public function __construct(UserPasswordEncoderInterface $passwordEncoder)
        {
            $this->passwordEncoder = $passwordEncoder;
        }

        /**
         * @return array|void
         */
        public static function getSubscribedEvents()
        {
            return [
                KernelEvents::VIEW => ['hashPassword', EventPriorities::PRE_WRITE],
            ];
        }

        /**
         * @param ViewEvent $event
         */
        public function hashPassword(ViewEvent $event): void
        {
            $user = $event->getControllerResult();
            $method = $event->getRequest()->getMethod();
            if (!$user instanceof User || !in_array($method, [Request::METHOD_POST], true)) {
                return;
            }

            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
        }

    }