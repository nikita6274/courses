<?php

namespace Nikita\Store\Controller\Autocomplete;

use Magento\Framework\App\Action\Action;

class Index extends Action
{
    protected $resultJsonFactory;
    protected $productCollection;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection,
        \Magento\Framework\Json\Helper\Data $jsonEncoder
    ) {

        $this->resultJsonFactory = $resultJsonFactory;
        $this->productCollection = $productCollection;
        $this->jsonEncoder = $jsonEncoder;
        parent::__construct($context);
    }

    public function execute()
    {
        $sku = $this->getRequest()->getParam('sku');
        $productArray = $this->productCollection->addFieldToFilter('sku', ['like' => "%$sku%"]);
        $this->productCollection->getSelect()->columns('sku');
        $productArray->load();
        foreach ($productArray as $product) {
            $skuArray[] = $product->getData('sku');
        }

        $json = $this->jsonEncoder->jsonEncode($skuArray);
        $this->getResponse()->setBody($json);
    }
}