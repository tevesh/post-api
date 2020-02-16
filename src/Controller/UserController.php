<?php

    namespace App\Controller;

    use App\Service\UserConfirmationService;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\Routing\Annotation\Route;

    /**
     * Class UserController
     * @package App\Controller
     */
    class UserController extends AbstractController
    {
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

            return $this->redirectToRoute('default_index');
        }
    }