<?php

namespace Nikita\Store\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{

    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Response
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param \Nikita\Store\Helper\Data $helperData
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response,
        \Nikita\Store\Helper\Data $helperData)
    {
        $this->actionFactory = $actionFactory;
        $this->_response = $response;
        $this->helperData = $helperData;
    }

    /**
     * Validate and Match
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        if (strpos($identifier, 'exampletocms') !== false) {
            $request->setModuleName('cms')->setControllerName('page')->setActionName('view')->setParam('page_id', 4);
        } else if (strpos($identifier, $this->helperData->getFrontUrl()) !== false) {
            $request->setModuleName('mystore')->setControllerName('test')->setActionName('test');
        } else {
            return;
        }

        return $this->actionFactory->create('Magento\Framework\App\Action\Forward', ['request' => $request]);
    }

}

