<?php


    namespace App\EventSubscriber;


    use ApiPlatform\Core\EventListener\EventPriorities;
    use App\Entity\User;
    use App\Security\TokenGenerator;
    use App\Service\MailerService;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpKernel\Event\ViewEvent;
    use Symfony\Component\HttpKernel\KernelEvents;
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
    use Twig\Error\LoaderError;
    use Twig\Error\RuntimeError;
    use Twig\Error\SyntaxError;

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

        /** @var MailerService $mailer */
        private $mailer;

        public function __construct(UserPasswordEncoderInterface $passwordEncoder, TokenGenerator $tokenGenerator, MailerService $mailer)
        {
            $this->passwordEncoder = $passwordEncoder;
            $this->tokenGenerator = $tokenGenerator;
            $this->mailer = $mailer;
        }

        /**
         * @return array
         */
        public static function getSubscribedEvents(): array
        {
            return [
                KernelEvents::VIEW => ['registerUser', EventPriorities::PRE_WRITE],
            ];
        }

        /**
         * @param ViewEvent $event
         *
         * @throws LoaderError
         * @throws RuntimeError
         * @throws SyntaxError
         */
        public function registerUser(ViewEvent $event): void
        {
            $user = $event->getControllerResult();
            $method = $event->getRequest()->getMethod();
            if (!$user instanceof User || $method !== Request::METHOD_POST) {
                return;
            }

            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
            $user->setConfirmationToken($this->tokenGenerator->getRandomSecureToken());

            $this->mailer->sendConfirmationEmail($user);
        }

    }