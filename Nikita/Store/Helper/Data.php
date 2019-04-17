<?php
namespace Nikita\Store\Helper;

class Data extends \Magento\Framework\Model\AbstractModel
{
    protected $_scopeConfig;
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->_scopeConfig = $scopeConfig;
    }
    public function getFrontUrl()
    {
        $myvalue = $this->_scopeConfig->getValue('customsetting/general/fronturl', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $myvalue;
    }
}