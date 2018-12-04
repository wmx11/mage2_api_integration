<?php

namespace Vipps\SignupIntegration\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class AdminConfig extends AbstractHelper
{
    const XML_PATH_FEATURED = 'signupintegration/';


    public function getConfigValues($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    public function getConfigFieldValue($code, $storeId = null)
    {
        return $this->getConfigValues(self::XML_PATH_FEATURED . 'general/' . $code, $storeId);
    }
}