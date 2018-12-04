<?php

namespace Vipps\SignupIntegration\Controller\Api;

use \Magento\Framework\App\CsrfAwareActionInterface;
use \Magento\Framework\App\Request\InvalidRequestException;
use \Magento\Framework\App\RequestInterface;
use \Magento\Framework\App\ResponseInterface;

class Register extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{
    protected $logger;
    protected $adminConfigValue;
    protected $validator;
    protected $config;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Vipps\SignupIntegration\Logger\Logger $logger,
        \Vipps\SignupIntegration\Helper\AdminConfig $adminConfigValue,
        \Vipps\SignupIntegration\Helper\Validator $validator,
        \Vipps\SignupIntegration\Helper\Config $config
    ) {
        $this->logger = $logger;
        $this->adminConfigValue = $adminConfigValue;
        $this->validator = $validator;
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
     * @return bool|ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */

    public function execute()
    {
        try {
            if ($this->validator->validateRequest() === true) {
                echo json_encode($this->config->getVippsPartialSignupArray());
                $this->logger->log('info', 'Merchant Json has been sent to Vipps');
            }
        } catch (\Exception $error) {
            $this->logger->log('error', $error->getMessage());
        }
    }
}