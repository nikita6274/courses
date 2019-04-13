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

    public function afterInitProduct(\Magento\Checkout\Controller\Cart\Add $subject, $result)
    {
        $sku = $this->request->getParam('sku');
        $result = $this->productRepository->get($sku);
        return $result;
    }

    public function execute()
    {

    }
}
