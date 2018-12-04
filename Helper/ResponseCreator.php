<?php
/**
 * Created by PhpStorm.
 * User: norbert
 * Date: 18.11.9
 * Time: 09.01
 */

namespace Vipps\SignupIntegration\Helper;


class ResponseCreator
{

    private $jsonResultFactory;
    protected $request;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory)
    {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->request = $request;
    }


    public function getAcceptedRequest()
    {
        $result = $this->jsonResultFactory->create();
        $result->setStatusHeader(202, null, 'Accepted');
        return $result;
    }

    public function getResponseForCallbackTrigger()
    {
        $result = $this->jsonResultFactory->create();
        //TODO Figure out response
        $result->setStatusHeader(200, null, 'RESPONSE???');
        return $result;
    }

    public function getBadRequest($msg = 'Bad Request')
    {
        $result = $this->jsonResultFactory->create();
        $result->setStatusHeader(400, null, $msg);
        return $result;
    }

    public function getUnauthorizedRequest()
    {
        $result = $this->jsonResultFactory->create();
        $result->setStatusHeader(401, null, 'Unauthorized');
        return $result;
    }

    public function getInternalServerErrorRequest()
    {
        $result = $this->jsonResultFactory->create();
        $result->setStatusHeader(500, null, 'Internal Server Error');
        return $result;
    }

    public function getServiceUnavailableRequest()
    {
        $result = $this->jsonResultFactory->create();
        $result->setStatusHeader(503, null, 'Service unavailable');
        return $result;
    }

    public function getGatewayTimeoutRequest()
    {
        $result = $this->jsonResultFactory->create();
        $result->setStatusHeader(504, null, 'Gateway Timeout');
        return $result;
    }


}