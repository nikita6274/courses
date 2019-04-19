<?php

namespace Nikita\Store\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;

class Cart extends \Magento\Framework\App\Action\Action
{
    protected $productRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->productRepository = $productRepository;
        $this->request = $request;
    }

    public function beforeExecute(\Magento\Checkout\Controller\Cart\Add $subject)
    {
        $sku = $this->request->getParam('sku');
        $product = $this->productRepository->get($sku);
        $productId = $product->getId();
        $this->request->setParam('product', $productId);
    }

    public function execute()
    {

    }
}
