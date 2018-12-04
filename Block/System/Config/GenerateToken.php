<?php

namespace Vipps\SignupIntegration\Block\System\Config;

class GenerateToken extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_template = 'Vipps_SignupIntegration::system/config/generate_token.phtml';
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

    public function getCallbackTokenHandlerUrl(): ?string
    {
        return $this->config->getInsertCallbackTokenUrl();
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
                'id' => 'generate_token',
                'label' => 'Generate Token'
            ]
        );

        return $button->toHtml();
    }
}