<?php

    declare(strict_types=1);

    namespace App\Serializer;

    use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
    use App\Entity\User;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

    /**
     * Class UserContextBuilder
     * @package App\Serializer
     */
    class UserContextBuilder implements SerializerContextBuilderInterface
    {
        /** @var SerializerContextBuilderInterface $decorated */
        protected $decorated;

        /** @var AuthorizationCheckerInterface $authorizationChecker */
        protected $authorizationChecker;

        /**
         * UserContextBuilder constructor.
         *
         * @param SerializerContextBuilderInterface $decorated
         * @param AuthorizationCheckerInterface $authorizationChecker
         */
        public function __construct(SerializerContextBuilderInterface $decorated, AuthorizationCheckerInterface $authorizationChecker)
        {
            $this->decorated = $decorated;
            $this->authorizationChecker = $authorizationChecker;
        }

        /**
         * @param Request $request
         * @param bool $normalization
         * @param array|null $extractedAttributes
         *
         * @return array
         */
        public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
        {
            $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);

            $resourceClass = $context['resource_class'] ?? null;
            if (User::class === $resourceClass && isset($context['groups']) && $normalization === true && $this->authorizationChecker->isGranted(User::ROLE_ADMIN)) {
                $context['groups'][] = 'get-admin';
            }

            return $context;
        }

    }