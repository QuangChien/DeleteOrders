<?php
/**
 * @author QuangChien(Glorious Victory) <quangchien01.it@gmail.com>
 * @copyright Copyright © 2023 QuangChien(Glorious Victory) <https://www.facebook.com/quangchien01>. All rights reserved.
 */

namespace Victory\DeleteOrders\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use \Victory\DeleteOrders\Model\Log;

class DeleteLogItem extends Action
{
    /**
     * @var Log
     */
    protected $log;

    /**
     * @param Context $context
     * @param Log $log
     */
    public function __construct(
        Context $context,
        Log $log
    ) {
        $this->log = $log;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('entity_id');
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if ($id) {
            try {
                $this->log->load($id)->delete();
                $this->messageManager->addSuccessMessage(__('The record has been deleted.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('An error occurred while deleting: %1',
                    $e->getMessage()));
            }
        } else {
            $this->messageManager->addErrorMessage(__('The record ID is missing.'));
        }
        return $resultRedirect->setRefererUrl();
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Victory_DeleteOrders::log_delete');
    }
}