<?php

    declare(strict_types=1);

    namespace App\Serializer;

    use App\Entity\User;
    use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
    use Symfony\Component\Serializer\Exception\ExceptionInterface;
    use Symfony\Component\Serializer\Exception\LogicException;
    use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
    use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
    use Symfony\Component\Serializer\SerializerAwareInterface;
    use Symfony\Component\Serializer\SerializerAwareTrait;
    use Symfony\Component\Serializer\SerializerInterface;

    /**
     * Class UserAttributeNormalizer
     * @package App\Serializer
     *
     * This class let modify how the entity should be returned during serialization
     */
    class UserAttributeNormalizer implements ContextAwareNormalizerInterface, SerializerAwareInterface
    {
        use SerializerAwareTrait;

        // Let call thi method just one time
        public const USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED = 'USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED';

        /** @var TokenStorageInterface $tokenStorage */
        private $tokenStorage;

        /**
         * UserAttributeNormalizer constructor.
         *
         * @param TokenStorageInterface $tokenStorage
         */
        public function __construct(TokenStorageInterface $tokenStorage)
        {
            $this->tokenStorage = $tokenStorage;
        }

        /**
         * @param User $object
         *
         * @return bool
         */
        private function userIsOwner(User $object): bool
        {
            $token = $this->tokenStorage->getToken();
            if (!$token) {
                return false;
            }

            return $object->getUsername() === $token->getUserName();
        }

        /**
         * @param $object
         * @param $format
         * @param array $context
         *
         * @return mixed
         * @throws ExceptionInterface
         */
        private function goAhead($object, $format, array $context)
        {
            if (!$this->serializer instanceof NormalizerInterface) {
                throw new LogicException(sprintf('Cannot normalize object "%s" because the injected serializer is not a normalizer.', $object));
            }

            $context[self::USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED] = true;

            return $this->serializer->normalize($object, $format, $context);
        }

        /**
         * @param mixed $data
         * @param null $format
         * @param array $context
         *
         * @return bool|void
         */
        public function supportsNormalization($data, $format = null, array $context = [])
        {
            if (isset($context[self::USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED])) {
                return false;
            }

            return $data instanceof User;
        }

        /**
         * @param mixed $object
         * @param null $format
         * @param array $context
         *
         * @return array|bool|float|int|string|void
         * @throws ExceptionInterface
         */
        public function normalize($object, $format = null, array $context = [])
        {
            if ($this->userIsOwner($object)) {
                $context['groups'][] = 'get-owner';
            }

            return $this->goAhead($object, $format, $context);
        }

        /**
         * @param SerializerInterface $serializer
         */
        public function setSerializer(SerializerInterface $serializer): void
        {
            $this->serializer = $serializer;
        }

    }