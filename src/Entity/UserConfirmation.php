<?php

    namespace App\Entity;

    use Symfony\Component\Validator\Constraints as Assert;

    /**
     * Class UserConfirmation
     * @package App\Entity
     */
    class UserConfirmation
    {
        /**
         * @Assert\NotBlank()
         * @Assert\Length(min=30, max=30)
         *
         * @var string $confirmationToken
         */
        public $confirmationToken;
    }