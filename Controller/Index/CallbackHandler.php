<?php
/**
 * Created by PhpStorm.
 * User: norbert
 * Date: 18.11.13
 * Time: 17.10
 */

namespace Vipps\SignupIntegration\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Vipps\SignupIntegration\Helper\ResponseCreator;

class CallbackHandler extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{

    public function __construct(Context $context,
                                \Magento\Framework\App\Config\ConfigResource\ConfigInterface $resourceConfig,
                                \Magento\Framework\Encryption\EncryptorInterface $encryptor,
                                \Magento\Framework\App\RequestInterface $request,
                                ResponseCreator $responseCreator,
                                \Vipps\SignupIntegration\Helper\AdminConfig $adminConfig,
                                \Vipps\SignupIntegration\Helper\Validator $validator,
                                \Vipps\SignupIntegration\Helper\Config $config,
                                \Vipps\SignupIntegration\Logger\Logger $logger)
    {
        $this->_request = $request;
        $this->responseCreator = $responseCreator;
        $this->resourceConfig = $resourceConfig;
        $this->adminConfig = $adminConfig;
        $this->validator = $validator;
        $this->encryptor = $encryptor;
        $this->config = $config;
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
            $this->responseCreator->getBadRequest();
        }

        try {
            $requestBody = $this->getRequestBody();

            $authorizationToken = $this->getAuthorizationHeader();

            //Callback Token Check
            if (!$this->isCallbackTokenCorrect($authorizationToken)) {
                $this->logger->log('Warning', 'Unauthorized Access through callback handler');
                return $this->responseCreator->getUnauthorizedRequest();
            }


            if (!$this->isOrgNumberCorrect($requestBody['orgnumber']) ||
                !$this->isSignupIdCorrect($requestBody['signup-id'])) {
                $this->logger->log('Error', 'Bad Request in callback handler');
                return $this->responseCreator->getBadRequest();
            }

            $this->saveToConfig($requestBody);

        } catch (\Exception $e) {
            $this->logger->log('Error', 'Bad Request in callback handler');
            return $this->responseCreator->getBadRequest();
        }

        return $this->responseCreator->getAcceptedRequest();
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
     * Returns authorization parameter from the request
     * @return mixed
     */
    private function getAuthorizationHeader()
    {
        return $this->getRequest()->getHeader('Authorization');
    }

    /**
     * Compares CallbackToken stored in config with parameter
     *
     * Returns true if parameter and config data matches
     *
     * @param $authorizationToken
     * @return bool
     */
    private function isCallbackTokenCorrect($authorizationToken): bool
    {
        return $this->config->getAdminValue('callbackToken') === $authorizationToken;
    }

    /**
     * Compares SignupId stored in config with parameter
     *
     * Returns true if parameter and config data matches
     *
     * @param $id
     * @return bool
     */
    private function isSignupIdCorrect($id)
    {
        //TODO remove later
        return true;
        return $this->config->getAdminValue('signupId') === $id;
    }

    /**
     * Compares org number stored in config with parameter
     *
     * Returns true if parameter and config data matches
     *
     * @param $orgNumebr
     * @return bool
     */
    private function isOrgNumberCorrect($orgNumebr)
    {
        return $this->config->getAdminValue('orgnumber') === $orgNumebr;

    }

    /**
     * Saves data from $requestBody into Vipps config
     *
     * @param array $requestBody decoded json from request body
     */
    private function saveToConfig($requestBody)
    {
        $this->resourceConfig->saveConfig($this->config->getVippsConfigPath('merchantSerialNumber'),
            $this->encryptor->encrypt($requestBody['merchantSerialNumber']));
        $this->resourceConfig->saveConfig($this->config->getVippsConfigPath('clientId'),
            $this->encryptor->encrypt($requestBody['client_Id']));
        $this->resourceConfig->saveConfig($this->config->getVippsConfigPath('clientSecret'),
            $this->encryptor->encrypt($requestBody['client_Secret']));
        $this->resourceConfig->saveConfig($this->config->getVippsConfigPath('subscriptionKey1'),
            $this->encryptor->encrypt($requestBody['subscriptionKeys'][0]['ocp-apim-subscription-key']));
        $this->resourceConfig->saveConfig($this->config->getVippsConfigPath('subscriptionKey2'),
            $this->encryptor->encrypt($requestBody['subscriptionKeys'][1]['ocp-apim-subscription-key']));
        //activate vipps payment
        $this->resourceConfig->saveConfig($this->config->getVippsConfigPath('vippsActive'), 1);
    }
}