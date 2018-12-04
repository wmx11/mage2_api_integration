<?php

namespace Vipps\SignupIntegration\Controller\Api;

use \Magento\Framework\App\CsrfAwareActionInterface;
use \Magento\Framework\App\Request\InvalidRequestException;
use \Magento\Framework\App\RequestInterface;
use \Magento\Framework\App\ResponseInterface;

class TestCatch extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{
    protected $logger;
    protected $adminConfigValue;
    protected $validator;
    protected $resourceConfig;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Vipps\SignupIntegration\Helper\AdminConfig $adminConfigValue,
        \Vipps\SignupIntegration\Helper\Validator $validator,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface  $resourceConfig
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
     * @return bool|ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */

    public function execute()
    {
        if (file_get_contents("php://input") !== null) {
            echo json_encode($this->callback());
        }
        return false;
    }

    public function callback()
    {
        $callbackArray = [
            "signup-id" => "thisisatest-00d0-488a-88b7-testukas35",
            "vippsURL" => "https://vippsbedrift.no/signup/vippspanett/?r=4188dea2-00d0-488a-88b7-bbanananana"
        ];
        return $callbackArray;
    }
}