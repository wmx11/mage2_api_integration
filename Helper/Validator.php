<?php

namespace Vipps\SignupIntegration\Helper;

class Validator
{
    const REQUEST_METHOD = "POST";

    /**
     * @return bool|null
     * @throws \Exception
     */

    public function validateAjaxRequest(): ?bool
    {
        if (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) {
            return true;
        } else {
            throw new \Exception("Invalid Request, Ajax Expected.");
        }
    }

    /**
     * @return bool|null
     * @throws \Exception
     */

    public function validateRequest(): ?bool
    {
        try {
            if ($this->getRequestMethod() === self::REQUEST_METHOD &&
                $this->validateAjaxRequest() === true
            ) {
                return true;
            }
        } catch (\Exception $error) {
            throw new \Exception($error->getMessage());
        }
    }

    /**
     * @param string $method
     * @return bool|null
     * @throws \Exception
     */

    public function validateRequestMethod(): ?bool
    {
        if ($this->getRequestMethod() === self::REQUEST_METHOD) {
            return true;
        } else {
            throw new \Exception("POST request expected. Received " . $_SERVER["REQUEST_METHOD"] . ".");
        }
    }

    public function getRequestMethod(): ?string
    {
        return $_SERVER["REQUEST_METHOD"];
    }
}