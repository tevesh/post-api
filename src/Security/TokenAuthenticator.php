<?php

    namespace App\Security;

    use App\Entity\User;
    use Lexik\Bundle\JWTAuthenticationBundle\Exception\ExpiredTokenException;
    use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\PreAuthenticationJWTUserToken;
    use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;
    use Symfony\Component\Security\Core\User\UserProviderInterface;

    /**
     * Class TokenAuthenticator
     * @package App\Security
     */
    class TokenAuthenticator extends JWTTokenAuthenticator
    {
        /**
         * Override the original JWTTokenAuthenticator to
         * add a layer to check token validity during password change
         *
         * @param PreAuthenticationJWTUserToken $preAuthToken
         * @param UserProviderInterface $userProvider
         *
         * @return User
         */
        public function getUser($preAuthToken, UserProviderInterface $userProvider): User
        {
            /** @var User $user */
            $user = parent::getUser($preAuthToken, $userProvider);
            /*
            Example of $preAuthToken->getPayload()
            [
                'iat' => 1581785190,
                'exp' => 1581788790,
                'roles' =>
                    [
                        0 => 'ROLE_COMMENTATOR',
                        'username' => 'HanSolo',
                    ],
            ]
            */
            if($user->getPasswordChangeDate() && $preAuthToken->getPayload()['iat'] < $user->getPasswordChangeDate()) {
                throw new ExpiredTokenException('Authentication token is expired');
            }

            return $user;
        }

    }