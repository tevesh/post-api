<?php


    namespace App\Controller;

    use ApiPlatform\Core\Validator\ValidatorInterface;
    use App\Entity\User;
    use Doctrine\ORM\EntityManagerInterface;
    use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

    /**
     * Class ResetPasswordAction
     * @package App\Controller
     */
    class ResetPasswordAction
    {

        /** @var ValidatorInterface $validator */
        private $validator;

        /** @var UserPasswordEncoderInterface $userPasswordEncoder */
        private $userPasswordEncoder;

        /** @var EntityManagerInterface $entityManager */
        private $entityManager;

        /** @var JWTTokenManagerInterface $tokenManager */
        private $tokenManager;

        /**
         * ResetPasswordAction constructor.
         * It's need to validate the data before save due to how to doctrine work
         *
         * @param ValidatorInterface $validator
         * @param UserPasswordEncoderInterface $userPasswordEncoder
         * @param EntityManagerInterface $entityManager
         * @param JWTTokenManagerInterface $tokenManager
         */
        public function __construct(ValidatorInterface $validator, UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager, JWTTokenManagerInterface $tokenManager)
        {
            $this->validator = $validator;
            $this->userPasswordEncoder = $userPasswordEncoder;
            $this->entityManager = $entityManager;
            $this->tokenManager = $tokenManager;
        }

        /**
         * Whole class is like a single method that should be called by creating a new instance
         *
         * @param User $user
         *
         * @return JsonResponse
         */
        public function __invoke(User $user)
        {
            // $reset = new ResetPasswordAction();
            // reset();
            $this->validator->validate($user);
            // encode password and set to the user
            $user->setPassword($this->userPasswordEncoder->encodePassword($user, $user->getNewPassword()));
            // After changing the password the actual authentication token need to be invalidated
            $user->setPasswordChangeDate(time());
            // directly persist the data instead of using normal symfony flow
            $this->entityManager->flush();
            // create a new one to prevent that user have to create a new token manually
            $token = $this->tokenManager->create($user);

            return new JsonResponse(['token', $token]);
        }
    }