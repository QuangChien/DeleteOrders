<?php
/**
 * @author QuangChien(Glorious Victory) <quangchien01.it@gmail.com>
 * @copyright Copyright © 2023 QuangChien(Glorious Victory) <https://www.facebook.com/quangchien01>. All rights reserved.
 */

namespace Victory\DeleteOrders\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Victory\DeleteOrders\Helper\Data;

class Log extends Action
{
    /**
     * @var PageFactory
     *
     */
    protected $resultPageFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $helperData
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        Data $helperData
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->helperData = $helperData;
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        if(!$this->helperData->isEnabled()) return $this->_response->setRedirect($this->_redirect->getRefererUrl());
        $resultPage = $this->resultPageFactory->create();
        $this->_setActiveMenu("Magento_Sales::sales_order");
        $resultPage->getConfig()->getTitle()->prepend(__('Order Delete Logs'));
        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Victory_DeleteOrders::log_list');
    }
}
