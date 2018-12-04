<?php

namespace Vipps\SignupIntegration\Controller\Api;

use \Magento\Framework\App\CsrfAwareActionInterface;
use \Magento\Framework\App\Request\InvalidRequestException;
use \Magento\Framework\App\RequestInterface;
use \Magento\Framework\App\ResponseInterface;

class TokenHandler extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{
    protected $logger;
    protected $adminConfigValue;
    protected $validator;
    protected $resourceConfig;
    protected $storeManager;
    protected $config;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Vipps\SignupIntegration\Helper\AdminConfig $adminConfigValue,
        \Vipps\SignupIntegration\Helper\Validator $validator,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $resourceConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Vipps\SignupIntegration\Helper\Config $config,
        \Vipps\SignupIntegration\Logger\Logger $logger
    ) {
        $this->logger = $logger;
        $this->adminConfigValue = $adminConfigValue;
        $this->validator = $validator;
        $this->resourceConfig = $resourceConfig;
        $this->storeManager = $storeManager;
        $this->config = $config;
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
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     * @throws \Exception
     */


    public function execute()
    {
        if ($this->validatePostRequest() === true) {
            $this->resourceConfig->saveConfig($this->config->getCallbackTokenSqlPath(), $this->getCallbackToken());
            $this->logger->log('info', 'New callback token has been generated: ' . $this->getCallbackToken());
        }
    }

    /**
     * @return bool|null
     * @throws \Exception
     */

    public function validatePostRequest(): ?bool
    {
        try {
            if ($this->getCallbackToken()) {
                return true;
            }
        } catch (\Exception $error) {
            $this->logger->log('error', $error->getMessage());
        }
    }

    /**
     * Gets token from requests POST variable array
     * Returns token if it exists, else returns false
     *
     * @return bool
     * @throws \Exception
     */

    public function getCallbackToken()
    {
        if (isset($_POST['token'])) {
            return $_POST['token'];
        } else {
            throw new \Exception
            (
                "POST request expected for TokenHandler. Received " . $_SERVER["REQUEST_METHOD"] . "."
            );
        }
    }
}