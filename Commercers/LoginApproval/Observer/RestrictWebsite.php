<?php
namespace Commercers\LoginApproval\Observer;
use Magento\Framework\Event\ObserverInterface;


class RestrictWebsite implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Response\RedirectInterface $redirect
    ) {
        $this->_customerSession = $customerSession;
        $this->redirect = $redirect;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $actionName = $observer->getEvent()->getRequest()->getFullActionName();
        $controller = $observer->getControllerAction();
        if($this->_customerSession->isLoggedIn()){
            return;
        }
        if($actionName == 'customer_account_login'){
            return;
        }
        $openActions = array(
            'create',
            'createpost',
            'login',
            'loginpost',
            'logoutsuccess',
            'forgotpassword',
            'forgotpasswordpost',
            'resetpassword',
            'resetpasswordpost',
            'confirm',
            'confirmation'
        );
        if ($controller == 'account' && in_array($actionName, $openActions)) {
            return $this; //if in allowed actions do nothing.
        }
        if(!$this->_customerSession->isLoggedIn()) {
            $this->redirect->redirect($controller->getResponse(), 'customer/account/login');
        }
    }
}