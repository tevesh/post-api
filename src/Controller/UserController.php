<?php

    namespace App\Controller;

    use App\Service\UserConfirmationService;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Contracts\Translation\TranslatorInterface;

    /**
     * Class UserController
     * @package App\Controller
     */
    class UserController extends AbstractController
    {
        /**
         * @var TranslatorInterface
         */
        private $translator;

        /**
         * UserController constructor.
         *
         * @param TranslatorInterface $translator
         */
        public function __construct(TranslatorInterface $translator){

            $this->translator = $translator;
        }
        /**
         * @Route("/confirm-user/{confirmationToken}", name="confirm_user", requirements={"confirmationToken"="\w{30}"})
         *
         * @param UserConfirmationService $userConfirmationService
         * @param string $confirmationToken
         *
         * @return RedirectResponse
         */
        public function confirm(UserConfirmationService $userConfirmationService, string $confirmationToken): RedirectResponse
        {
            $userConfirmationService->confirmUser($confirmationToken);

            $this->addFlash('success', $this->translator->trans('user.messages.confirmation.success'));

            return $this->redirectToRoute('default_index');
        }
    }