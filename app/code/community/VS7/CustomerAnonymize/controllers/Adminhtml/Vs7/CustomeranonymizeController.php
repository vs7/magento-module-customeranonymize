<?php
class VS7_CustomerAnonymize_Adminhtml_Vs7_CustomeranonymizeController extends Mage_Adminhtml_Controller_Action
{
    public function deleteCustomerAction() {
        if (!Mage::helper('vs7_customeranonymize')->isEnabled()) {
            return;
        }

        $customer = Mage::getModel('customer/customer')->load($this->getRequest()->getParam('id'));
        if (!$customer->getId()) {
            Mage::getSingleton('core/session')->addError('No customer ID provided.');
            $this->_redirect('adminhtml/dashboard/index');
            return;
        }

        Mage::helper('vs7_customeranonymize')->anonymizeCustomer($customer);

        $this->_redirect('adminhtml/customer/index');
        Mage::getSingleton('core/session')->addSuccess('The account has been successfully deleted, and all orders have been anonymized.');
        return;
    }

//    public function sendAnonymizeEmailAction() {
//        if (!Mage::helper('vs7_customeranonymize')->isEnabled()) {
//            return;
//        }
//
//        $customer = Mage::getModel('customer/customer')->load($this->getRequest()->getParam('id'));
//        if (!$customer->getId()) {
//            Mage::getSingleton('core/session')->addError('No customer ID provided.');
//            $this->_redirect('*');
//            return;
//        }
//
//        /** @var Mage_Core_Model_Email_Template $email */
//        $email = Mage::getModel('core/email_template')->loadDefault('vs7_customeranonymize_confirm');
//        if ($email->getId()) {
//            $email->sendTransactional($email->getId(), 'sales', $customer->getEmail(), $customer->getName(), array(
//                'email' => $customer->getEmail(),
//                'name' => $customer->getName(),
//                'link' => Mage::getUrl('vs7_customeranonymize/customer/deleteaccount')
//            ), 0);
//        }
//
//        $this->_redirect('adminhtml/customer/edit', array('id' => $customer->getId()));
//        Mage::getSingleton('core/session')->addSuccess('Anonymization email successfully sent.');
//    }

   protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('vs7_customeranonymize');
    }
}