<?php

    namespace App\Exception;

    use Exception;
    use Throwable;

    abstract class BaseCustomException extends Exception
    {
        protected const DEFAULT_MESSAGE = 'An exception occurred';

        /**
         * EmptyBodyException constructor.
         *
         * @param string $message
         * @param int $code
         * @param Throwable|null $previous
         */
        public function __construct(string $message, int $code = 0, Throwable $previous = null)
        {
            if (empty($message)) {
                $message = self::DEFAULT_MESSAGE;
            }

            parent::__construct($message, $code, $previous);
        }
    }