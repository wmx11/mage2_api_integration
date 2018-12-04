<?php

namespace Vipps\SignupIntegration\Block\System\Config;

class Register extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_template = 'Vipps_SignupIntegration::system/config/vipps_register.phtml';
    protected $config;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Vipps\SignupIntegration\Helper\Config $config,
        array $data = []
    ) {
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }


    /**
     * @return null|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */

    public function getJsonGeneratorUrl(): ?string
    {
        return $this->config->getCreateJsonControllerUrl();
    }

    /**
     * @return null|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */

    public function getVippsPartialSignupUrl(): ?string
    {
        return $this->config->getVippsPartialSignupUrl();
    }

    /**
     * @return null|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */

    public function getSignupIdHandlerUrl():? string
    {
        return $this->config->getSignupIdControllerUrl();
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'vipps_register',
                'label' => 'Generate Registration Link',
                'style' => 'background:#fe5b24; color:white; border:none'
            ]
        );

        return $button->toHtml();
    }
}