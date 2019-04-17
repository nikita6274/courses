<?php

namespace Nikita\Store\Model\ResourceModel;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    public function _construct()
    {
        $this->_init("Nikita\Store\Model\DataExample", "Nikita\Store\Model\ResourceModel\DataExample");
    }
}
