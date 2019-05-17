<?php

namespace Nikita\Store\Model\Rule\Condition;

class Customer extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $sourceYesno;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderFactory;

    /**
     * Constructor
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Config\Model\Config\Source\Yesno $sourceYesno
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Config\Model\Config\Source\Yesno $sourceYesno,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->sourceYesno = $sourceYesno;
        $this->orderFactory = $orderFactory;
    }

    public function loadAttributeOptions()
    {
        $this->setAttributeOption([
            'customer_first_order' => __('Maximum discount')
        ]);
        return $this;
    }

    public function getInputType()
    {
        return 'select';
    }

    public function getValueElementType()
    {
        return 'select';
    }

    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            $this->setData(
                'value_select_options',
                $this->sourceYesno->toOptionArray()
            );
        }
        return $this->getData('value_select_options');
    }

    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $customerId = $model->getCustomerId();
        $order = $this->orderFactory->create()
            ->addAttributeToSelect('customer_id')
            ->addFieldToFilter('customer_id',['eq' => $customerId])
            ->getFirstItem();

        $firstOrder = 1;
        if ($order->getId()) {
            $firstOrder = 0;
        }
        $model->setData('customer_first_order', $firstOrder);
        return parent::validate($model);
    }
}
