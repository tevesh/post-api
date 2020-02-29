<?php

    namespace App\Exception;

    /**
     * Class EmptyBodyException
     * @package App\Exception
     */
    class EmptyBodyException extends BaseCustomException
    {
        protected const DEFAULT_MESSAGE = 'Body of POST/PUT methods cannot be empty';
    }