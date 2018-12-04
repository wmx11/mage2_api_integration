<?php

namespace Vipps\SignupIntegration\Controller\Api;

use \Magento\Framework\App\CsrfAwareActionInterface;
use \Magento\Framework\App\Request\InvalidRequestException;
use \Magento\Framework\App\RequestInterface;
use \Magento\Framework\App\ResponseInterface;
use \Vipps\SignupIntegration\Helper\Config as config;

class SignupIdHandler extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{
    protected $logger;
    protected $adminConfigValue;
    protected $validator;
    protected $resourceConfig;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Vipps\SignupIntegration\Logger\Logger $logger,
        \Vipps\SignupIntegration\Helper\AdminConfig $adminConfigValue,
        \Vipps\SignupIntegration\Helper\Validator $validator,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $resourceConfig
    ) {
        $this->logger = $logger;
        $this->adminConfigValue = $adminConfigValue;
        $this->validator = $validator;
        $this->resourceConfig = $resourceConfig;
        parent::__construct($context);
    }

    /**
     * Create exception in case CSRF validation failed.
     * Return null if default exception will suffice.
     *
     * @param RequestInterface $request
     *
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * Perform custom request validation.
     * Return null if default validation is needed.
     *
     * @param RequestInterface $request
     *
     * @return bool|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Exception
     */

    public function execute()
    {
        if ($this->validateRequestBody() === true && $this->validateSignupUrlPostRequest() !== true) {
            $callbackArray = $this->getRequestBody();
            try {
                if ($this->validateJsonString($callbackArray) === true) {
                    $this->insertSignupIdToConfig($callbackArray);
                    echo $this->jsonStringToArray($callbackArray)[0];
                    $this->logger->log(
                        'info',
                        'Vipps Signup ID Successfully Inserted: ' .
                        $this->jsonStringToArray($callbackArray)[0]
                    );
                }
            } catch (\Exception $error) {
                $this->logger->log('error', $error->getMessage());
            }
        } elseif ($this->validateSignupUrlPostRequest() === true) {
            $this->insertSignupUrl();
        }
    }

    public function insertSignupUrl()
    {
        if ($this->validateSignupUrlPostRequest() === true) {
            return $this->resourceConfig->saveConfig(config::FIELD_ADMIN_SIGNUP_LINK, $_POST["url"]);
        }
    }

    public function validateSignupUrlPostRequest(): ?bool
    {
        if (isset($_POST["url"])) {
            return true;
        }
        return false;
    }

    /**
     * Validation for request body not being null
     *
     * @return bool|null
     */

    public function validateRequestBody(): ?bool
    {
        if (file_get_contents("php://input") !== null) {
            return true;
        }
        return false;
    }

    /**
     * Returns request body as a single string
     *
     * @return bool|string
     */

    public function getRequestBody()
    {
        return file_get_contents("php://input");
    }

    /**
     * Converts Single JSON string to assoc array
     *
     * @param $jsonString
     * @return array|null
     */

    public function jsonStringToArray($jsonString): ?array
    {
        if (!empty($jsonString)) {
            return explode("&", $jsonString);
        }
    }

    /**
     * Saves SingupID in the module config
     *
     * @param $jsonString
     * @throws \Exception
     */

    public function insertSignupIdToConfig($jsonString)
    {
        if ($this->validateJsonString($jsonString) === true) {
            $this->resourceConfig->saveConfig
            (
                config::FIELD_ADMIN_SIGNUP_ID,
                $this->jsonStringToArray($jsonString)[0]
            );
        }
    }

    /**
     * Validates JSON String
     *
     * @param $jsonString
     * @return bool
     * @throws \Exception
     */

    public function validateJsonString($jsonString): ?bool
    {
        if (!empty($jsonString) &&
            $this->jsonStringToArray($jsonString)[0] !== null ||
            $this->jsonStringToArray($jsonString)[0] !== ""
        ) {
            return true;
        } else {
            throw new \Exception("Invalid Json String");
        }
    }
}