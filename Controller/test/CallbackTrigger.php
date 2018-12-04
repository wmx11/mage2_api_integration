<?php

namespace Vipps\SignupIntegration\Controller\Test;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Vipps\SignupIntegration\Helper\ResponseCreator;

class CallbackTrigger extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{
    public function __construct(Context $context,
                                \Magento\Framework\App\Config\ConfigResource\ConfigInterface $resourceConfig,
                                \Magento\Framework\App\RequestInterface $request,
                                ResponseCreator $responseCreator,
                                \Vipps\SignupIntegration\Helper\Validator $validator,
                                \Vipps\SignupIntegration\Helper\Config $config,
                                \Vipps\SignupIntegration\Logger\Logger $logger)
    {
        $this->_request = $request;
        $this->responseCreator = $responseCreator;
        $this->resourceConfig = $resourceConfig;
        $this->config = $config;
        $this->validator = $validator;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        if (!$this->validator->validateRequestMethod()) {
            $this->logger->log('Error', 'Bad Request in callback trigger');
            return $this->responseCreator->getBadRequest();
        }
        try {
            $requestBody = $this->getRequestBody();


            if ((!$this->isSignupCallbackUrlCorrect($requestBody['signupCallbackUrl']))
                || (!$this->isCallbackTokenCorrect($requestBody['signupCallbackToken']))) {
                $this->logger->log('Error', 'Bad request (Missing a required parameter or Bad request format) in callback trigger');
                return $this->responseCreator->getBadRequest('Bad request (Missing a required parameter or bad request format)');
            }

            return $this->responseCreator->getAcceptedRequest();


        } catch (\Exception $e) {
            $this->logger->log('Error', 'Bad Request in callback trigger');
            return $this->responseCreator->getBadRequest();
        }

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
     * Reads received request body
     *
     * @return mixed
     */
    private function getRequestBody()
    {
        $requestBody = file_get_contents("php://input");

        return json_decode($requestBody, true);
    }

    /**
     * Compares SignupCallbackUrl stored in config with parameter
     *
     * Returns true if parameter and config data matches
     *
     * @return bool
     */
    private function isSignupCallbackUrlCorrect($url)
    {
        return $this->config->getAdminValue('callbackUrl') === $url;
    }

    /**
     * Compares CallbackToken stored in config with parameter
     *
     * Returns true if parameter and config data matches
     *
     * @return bool
     */
    private function isCallbackTokenCorrect($token)
    {
        return $this->config->getAdminValue('callbackToken') === $token;
    }
}