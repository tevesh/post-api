<?php

    namespace App\Exception;

    /**
     * Class InvalidConfirmationTokenException
     * @package App\Exception
     */
    class InvalidConfirmationTokenException extends BaseCustomException
    {
        protected const DEFAULT_MESSAGE = 'Invalid confirmation token';
    }