<?php

namespace Nikita\Store\Model\Quote;

use Magento\Checkout\Model\Session;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\SalesRule\Model\Validator;
use Magento\Store\Model\StoreManagerInterface;
use Nikita\Store\Helper\Data;

class Discount extends AbstractTotal
{
    protected $_checkoutSession;
    /**
     * Discount calculation object
     *
     * @var Validator
     */
    protected $calculator;
    /**
     * Core event manager proxy
     *
     * @var ManagerInterface
     */
    protected $eventManager = null;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    public function __construct(
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        Validator $validator,
        PriceCurrencyInterface $priceCurrency,
        Data $configData,
        Session $checkoutSession
    ) {
        $this->setCode('testdiscount');
        $this->eventManager = $eventManager;
        $this->calculator = $validator;
        $this->storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
        $this->configData = $configData;
        $this->checkoutSession = $checkoutSession;
    }

    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        $discount = $this->configData->getDiscount();
        $count = $this->configData->getCount();
        $qtyApply = $this->configData->getQty();
        $maxDiscount = $this->configData->getMaxDiscount();
        $cartData = $this->checkoutSession->getQuote()->getItemsQty();
        $cartItems = $this->checkoutSession->getQuote()->getAllItems();
        $cartDataCount = $cartData;
        $result = (int)($cartDataCount / $count);
        $test = 0;

        switch (true) {
            case (($result <= $qtyApply) && ($result >= 1)):
                $test = $result;
                break;
            case ($result > $qtyApply):
                $test = $qtyApply;
                break;
        }

        $test = $test * $discount;
        if ($test <= $maxDiscount) {
            $baseDiscountAmount = $test;
        } else {
            $baseDiscountAmount = $maxDiscount;
        }

        $discountItem = $baseDiscountAmount / $cartData;
        foreach ($cartItems as $item) {
            if ($item->getDiscountDescription()) {
                $discountItem = $item->getDiscountAmount() + $discountItem;
            }

            $discountAmount = $this->priceCurrency->convert($discountItem);
            $item->setDiscountAmount(-$discountAmount);
            $item->setBaseDiscountAmount(-$discountItem);
        }

        $discountAmount = $this->priceCurrency->convert($baseDiscountAmount);
        $total->setDiscountAmount(-$discountAmount);
        $total->setBaseDiscountAmount(-$baseDiscountAmount);
        $total->setSubtotalWithDiscount($total->getSubtotal() - $baseDiscountAmount);
        $total->setBaseSubtotalWithDiscount($total->getBaseSubtotal() - $baseDiscountAmount);
        $total->addTotalAmount($this->getCode(), -$baseDiscountAmount);
        $total->addBaseTotalAmount($this->getCode(), -$baseDiscountAmount);

        return $this;
    }

    /**
     * Add discount total information to address
     *
     * @param Quote $quote
     * @param Total $total
     * @return array|null
     */
    public function fetch(Quote $quote, Total $total)
    {
        $result = null;
        $amount = $total->getDiscountAmount();

        if ($amount != 0) {
            $description = $total->getDiscountDescription();
            $result = [
                'code' => $this->getCode(),
                'title' => strlen($description) ? __('Discount (%1)', $description) : __('Discount'),
                'value' => $amount
            ];
        }
        return $result;
    }
}
