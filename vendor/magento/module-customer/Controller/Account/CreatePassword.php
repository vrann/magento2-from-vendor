<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Customer\Controller\Account;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

class CreatePassword extends \Magento\Customer\Controller\Account
{
    /** @var AccountManagementInterface */
    protected $customerAccountManagement;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     * @param AccountManagementInterface $customerAccountManagement
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        AccountManagementInterface $customerAccountManagement
    ) {
        $this->customerAccountManagement = $customerAccountManagement;
        parent::__construct($context, $customerSession, $resultPageFactory);
    }

    /**
     * Resetting password handler
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resetPasswordToken = (string)$this->getRequest()->getParam('token');
        $customerId = (int)$this->getRequest()->getParam('id');
        try {
            $this->customerAccountManagement->validateResetPasswordLinkToken($customerId, $resetPasswordToken);

            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getLayout()->getBlock('resetPassword')->setCustomerId($customerId)
                ->setResetPasswordLinkToken($resetPasswordToken);
            return $resultPage;
        } catch (\Exception $exception) {
            $this->messageManager->addError(__('Your password reset link has expired.'));
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/forgotpassword');
            return $resultRedirect;
        }
    }
}
