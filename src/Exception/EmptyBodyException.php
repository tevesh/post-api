<?php

    namespace App\Exception;

    use Exception;
    use Throwable;

    /**
     * Class EmptyBodyException
     * @package App\Exception
     */
    class EmptyBodyException extends Exception
    {
        /**
         * EmptyBodyException constructor.
         *
         * @param string $message
         * @param int $code
         * @param Throwable|null $previous
         */
        public function __construct(string $message, int $code, Throwable $previous = null)
        {
            parent::__construct('Body of POST/PUT methods cannot be empty', $code, $previous);
        }
    }