<?php

namespace Nikita\Store\Controller\Add;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;

class Add extends \Magento\Checkout\Controller\Cart
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param CustomerCart $cart
     * @param ProductRepositoryInterface $productRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerCart $cart,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\App\RequestInterface $request,
        \Nikita\Store\Model\DataExampleFactory $dataExample
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->_dataExample = $dataExample;
    }

    /**
     * Add product to shopping cart action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $sku = $this->request->getParam('sku');
        $params = $this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new \Zend_Filter_LocalizedToNormalized([
                    'locale' => $this->_objectManager->get(\Magento\Framework\Locale\ResolverInterface::class)->getLocale()
                ]);
                $params['qty'] = $filter->filter($params['qty']);
            }
            $product = $this->productRepository->get($sku);
            $related = $this->getRequest()->getParam('related_product');

            $this->cart->addProduct($product, $params);
            if (!empty($related)) {
                $this->cart->addProductsByIds(explode(',', $related));
            }

            $this->cart->save();
            $this->addToDb($product->getName(), $sku, 1);
            return $this->goBack(null, $product);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($this->_checkoutSession->getUseNotice(true)) {
                $this->messageManager->addNotice($this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($e->getMessage()));
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->messageManager->addError($this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($message));
                }
            }
        }
    }

    public function addToDb($name, $sku, $qty)
    {
        $model = $this->_dataExample->create();
        $model->addData([
            "product_name" => $name,
            "sku" => $sku,
            "qty" => $qty,
            "sort_order" => 1
        ]);
        $model->save();

    }

    public function goBack($backUrl = null, $product = null)
    {
        if (!$this->getRequest()->isAjax()) {
            return parent::_goBack($backUrl);
        }

        $result = [];

        if ($backUrl || $backUrl = $this->getBackUrl()) {
            $result['backUrl'] = $backUrl;
        } else {
            if ($product && !$product->getIsSalable()) {
                $result['product'] = [
                    'statusText' => __('Out of stock')
                ];
            }
        }

        $this->getResponse()->representJson($this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($result));
    }
}
