<?php

    namespace App\Security;

    use Exception;

    /**
     * Class TokenGenerator
     * @package App\Security
     */
    class TokenGenerator
    {
        private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        /**
         * @return string
         */
        private function getRandomLetterFromAlphabet(): string
        {
            try {
                return self::ALPHABET[random_int(0, strlen(self::ALPHABET) - 1)];
            } catch (Exception $exception) {
                $this->getRandomLetterFromAlphabet();
            }
        }

        /**
         * @param int $length
         *
         * @return string
         */
        public function getRandomSecureToken(int $length = 30): string
        {
            $token = '';
            for ($i = 0; $i < $length; $i++) {
                $token .= $this->getRandomLetterFromAlphabet();
            }

            return $token;
        }
    }