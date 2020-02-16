<?php

    namespace App\Service;

    use Swift_Mailer;
    use Swift_Message;
    use Symfony\Component\Security\Core\User\UserInterface;
    use Symfony\Contracts\Translation\TranslatorInterface;
    use Twig\Environment;
    use Twig\Error\LoaderError;
    use Twig\Error\RuntimeError;
    use Twig\Error\SyntaxError;

    /**
     * Class MailerService
     * @package App\Service
     */
    class MailerService
    {
        /**
         * @var Swift_Mailer
         */
        private $mailer;
        /**
         * @var Environment
         */
        private $environment;
        /**
         * @var TranslatorInterface
         */
        private $translator;

        /**
         * MailerService constructor.
         *
         * @param Swift_Mailer $mailer
         * @param Environment $environment
         * @param TranslatorInterface $translator
         */
        public function __construct(Swift_Mailer $mailer, Environment $environment, TranslatorInterface $translator)
        {
            $this->mailer = $mailer;
            $this->environment = $environment;
            $this->translator = $translator;
        }

        /**
         * @param UserInterface $user
         *
         * @throws LoaderError
         * @throws RuntimeError
         * @throws SyntaxError
         */
        public function sendConfirmationEmail(UserInterface $user): void
        {
            $params = [
                'user'           => $user,
                'welcomeMessage' => sprintf($this->translator->trans('user.email.confirmation.welcome'), $user->getUsername()),
                'bodyMessage'    => sprintf($this->translator->trans('user.email.confirmation.body'), $user->getConfirmationToken()),
            ];

            $body = $this->environment->render('email/confirmation.html.twig', $params);

            $message = new Swift_Message();
            $message->setSubject($this->translator->trans('user.email.confirmation.subject'));
            $message->setBody($body, 'text/html');
            $message->setFrom(getenv('MAILER_USER'));
            $message->setTo($user->getEmail());

            $this->mailer->send($message);
        }
    }