<?php

namespace Nikita\Store\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractModel
{
    protected $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getFrontUrl()
    {
        $myvalue = $this->scopeConfig->getValue('customsetting/general/fronturl', ScopeInterface::SCOPE_STORE);
        return $myvalue;
    }

    public function getDiscount()
    {
        $discount = $this->scopeConfig->getValue('discount/general/discount', ScopeInterface::SCOPE_STORE);
        return $discount;
    }

    public function getCount()
    {
        $count = $this->scopeConfig->getValue('discount/general/count', ScopeInterface::SCOPE_STORE);
        return $count;
    }

    public function getQty()
    {
        $qtyApply = $this->scopeConfig->getValue('discount/general/qty', ScopeInterface::SCOPE_STORE);
        return $qtyApply;
    }

    public function getMaxDiscount()
    {
        $maxDiscount = $this->scopeConfig->getValue('discount/general/maxdiscount', ScopeInterface::SCOPE_STORE);
        return $maxDiscount;
    }
}
