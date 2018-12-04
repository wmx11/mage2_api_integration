<?php

namespace Vipps\SignupIntegration\Helper;

class Config
{
    const FIELD_VIPPS_ORGANIZATION_NUMBER = "orgnumber";
    const FIELD_VIPPS_PARTNER_ID = "partnerId";
    const FIELD_VIPPS_SUBSCRIPTION_PACK_ID = "subscriptionPackageId";
    const FIELD_VIPPS_MERCHANT_WEBSITE = "merchantWebsiteUrl";
    const FIELD_VIPPS_CALLBACK_TOKEN = "signupCallbackToken";
    const FIELD_VIPPS_CALLBACK_URL = "signupCallbackUrl";
    const FIELD_VIPPS_FROM_TYPE = "form-type";

    const FIELD_ADMIN_ORGANIZATION_NUMBER = "orgnumber";
    const FIELD_ADMIN_PARTNER_ID = "partnerId";
    const FIELD_ADMIN_SUBSCRIPTION_PACK_ID = "pricePackage";
    const FIELD_ADMIN_MERCHANT_WEBSITE = "merchantWebsite";
    const FIELD_ADMIN_CALLBACK_TOKEN = "callbackToken";
    const FIELD_ADMIN_CALLBACK_URL = "callbackUrl";
    const FIELD_ADMIN_FORM_TYPE = "vippspanett";
    const FIELD_ADMIN_SQL_CALLBACK_TOKEN = "v1/general/callbackToken";

    const FIELD_ADMIN_SIGNUP_ID = "v1/general/signupId";
    const FIELD_ADMIN_SIGNUP_LINK = "v1/general/signupLink";

    const PATH_CREATE_JSON_CONTROLLER = "v1/api/register";
    const PATH_INSERT_SIGNUP_ID_CONTROLLER = "v1/api/signupidhandler";
    const PATH_INSERT_CALLBACK_TOKEN = "v1/api/tokenhandler";

    const URL_VIPPS_PARTIAL_SIGNUP = "v1/api/testcatch";
    const URL_VIPPS_CALLBACK = "v1/Index/callbackhandler";

    const VIPPS_PATHNAME_MERCHANT_SERIAL_NUMBER = "merchantSerialNumber";
    const VIPPS_PATHNAME_CLIENT_ID = "clientId";
    const VIPPS_PATHNAME_CLIENT_SECRET = "clientSecret";
    const VIPPS_PATHNAME_SUBSCRIPTION_KEY1 = "subscriptionKey1";
    const VIPPS_PATHNAME_SUBSCRIPTION_KEY2 = "subscriptionKey2";
    const VIPPS_PATHNAME_VIPPS_ACTIVE = "vippsActive";

    const VIPPS_PATH_MERCHANT_SERIAL_NUMBER = "payment/vipps/merchant_serial_number";
    const VIPPS_PATH_CLIENT_ID = "payment/vipps/client_id";
    const VIPPS_PATH_CLIENT_SECRET = "payment/vipps/client_secret";
    const VIPPS_PATH_SUBSCRIPTION_KEY1 = "payment/vipps/subscription_key1";
    const VIPPS_PATH_SUBSCRIPTION_KEY2 = "payment/vipps/subscription_key2";
    const VIPPS_PATH_VIPPS_ACTIVE = "payment/vipps/active";

    private $vippsPartialSignupArray = [];
    private $vippsConfigPaths = [];
    private $config;
    protected $storeManager;

    public function __construct(
        \Vipps\SignupIntegration\Helper\AdminConfig $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->setVippsPartialSignupArray();
        $this->setVippsConfigPathsArray();
    }

    /**
     * Sets up an array with field values for partial vipps signup
     */
    private function setVippsPartialSignupArray()
    {
        $this->vippsPartialSignupArray = [
            self::FIELD_VIPPS_ORGANIZATION_NUMBER => $this->getAdminValue(self::FIELD_ADMIN_ORGANIZATION_NUMBER),
            self::FIELD_VIPPS_PARTNER_ID => $this->getAdminValue(self::FIELD_ADMIN_PARTNER_ID),
            self::FIELD_VIPPS_SUBSCRIPTION_PACK_ID => $this->getAdminValue(self::FIELD_ADMIN_SUBSCRIPTION_PACK_ID),
            self::FIELD_VIPPS_MERCHANT_WEBSITE => $this->getAdminValue(self::FIELD_ADMIN_MERCHANT_WEBSITE),
            self::FIELD_VIPPS_CALLBACK_TOKEN => $this->getAdminValue(self::FIELD_ADMIN_CALLBACK_TOKEN),
            self::FIELD_VIPPS_CALLBACK_URL => $this->getCallbackUrl(),
            self::FIELD_VIPPS_FROM_TYPE => self::FIELD_ADMIN_FORM_TYPE
        ];
    }

    /**
     * Sets up an array with paths to the config fields in magento
     */
    private function setVippsConfigPathsArray()
    {
        $this->vippsConfigPaths = [
            self::VIPPS_PATHNAME_MERCHANT_SERIAL_NUMBER => self::VIPPS_PATH_MERCHANT_SERIAL_NUMBER,
            self::VIPPS_PATHNAME_CLIENT_ID => self::VIPPS_PATH_CLIENT_ID,
            self::VIPPS_PATHNAME_CLIENT_SECRET => self::VIPPS_PATH_CLIENT_SECRET,
            self::VIPPS_PATHNAME_SUBSCRIPTION_KEY1 => self::VIPPS_PATH_SUBSCRIPTION_KEY1,
            self::VIPPS_PATHNAME_SUBSCRIPTION_KEY2 => self::VIPPS_PATH_SUBSCRIPTION_KEY2,
            self::VIPPS_PATHNAME_VIPPS_ACTIVE => self::VIPPS_PATH_VIPPS_ACTIVE
        ];
    }

    /**
     * Returns partial signup array
     *
     * @return array|null
     */
    public function getVippsPartialSignupArray(): ?array
    {
        return $this->vippsPartialSignupArray;
    }

    /**
     * Returns value of the field from config
     *
     * @param string $fieldName
     * @return null|string
     */
    public function getAdminValue(string $fieldName): ?string
    {
        return $this->config->getConfigFieldValue($fieldName);
    }

    /**
     * @return null|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */

    public function getInsertCallbackTokenUrl(): ?string
    {
        return $this->validateGeneratedUrl(self::PATH_INSERT_CALLBACK_TOKEN);
    }

    /**
     * @return null|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */

    public function getCreateJsonControllerUrl(): ?string
    {
        return $this->validateGeneratedUrl(self::PATH_CREATE_JSON_CONTROLLER);
    }

    /**
     * @return null|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */

    public function getVippsPartialSignupUrl(): ?string
    {
        return $this->validateGeneratedUrl(self::URL_VIPPS_PARTIAL_SIGNUP);
    }

    public function getCallbackUrl()
    {
        return $this->validateGeneratedUrl(self::URL_VIPPS_CALLBACK);
    }

    /**
     * @return null|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */

    public function getSignupIdControllerUrl(): ?string
    {
        return $this->validateGeneratedUrl(self::PATH_INSERT_SIGNUP_ID_CONTROLLER);
    }

    /**
     * @return null|string
     */

    public function getCallbackTokenSqlPath(): ?string
    {
        return self::FIELD_ADMIN_SQL_CALLBACK_TOKEN;
    }

    /**
     * @param string $pathToUrl
     * @return null|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */

    public function validateGeneratedUrl(string $pathToUrl): ?string
    {
        if (substr($this->getBaseUrl(), -1) === "/") {
            return $this->getBaseUrl() . $pathToUrl;
        } else {
            return $this->getBaseUrl() . "/" . $pathToUrl;
        }
    }

    /**
     * @return null|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */

    public function getBaseUrl(): ?string
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }

    /**
     * Returns pathname in config for vipps specific fields
     *
     * @param $pathname
     * @return mixed
     */

    public function getVippsConfigPath($pathname)
    {
        return $this->vippsConfigPaths[$pathname];
    }
}