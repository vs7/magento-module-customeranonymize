<?php

class VS7_CustomerAnonymize_CustomerController extends Mage_Core_Controller_Front_Action
{
    public function deleteconfirmationAction()
    {
        if (!Mage::helper('vs7_customeranonymize')->isEnabled()) {
            return;
        }

        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('customer/account/login');
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    public function deleteaccountAction()
    {
        if (!Mage::helper('vs7_customeranonymize')->isEnabled()) {
            return;
        }

        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if (!$customer) {
            Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('vs7_customeranonymize/customer/deleteaccount'));
            $this->_redirect('customer/account/login');
            return;
        }

        Mage::register('isSecureArea', true);
        Mage::helper('vs7_customeranonymize')->anonymizeCustomer($customer);
        Mage::unregister('isSecureArea');

        Mage::getSingleton('core/session')->clear();
        $this->_redirect('/index.php');

        $successMessage = Mage::helper('vs7_customeranonymize')->getSuccessMessage();
        if ($successMessage != null) {
            Mage::getSingleton('core/session')->addSuccess($successMessage);
            return;
        }
        Mage::getSingleton('core/session')->addSuccess('Your account has been successfully deleted, and all order details have been anonymized.');
        return;
    }
}