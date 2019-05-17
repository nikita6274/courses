<?php

namespace Nikita\Store\Observer;

class CustomerConditionObserver implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $additional = $observer->getAdditional();
        $conditions = (array) $additional->getConditions();

        $conditions = array_merge_recursive($conditions, [
            $this->getCustomerFirstOrderCondition()
        ]);

        $additional->setConditions($conditions);
        return $this;
    }

    private function getCustomerFirstOrderCondition()
    {
        return [
            'label'=> __('Maximum discount'),
            'value'=> \Nikita\Store\Model\Rule\Condition\Customer::class
        ];
    }
}