<?php

class VS7_CustomerAnonymize_Model_Observer
{
    public function addAnonymizeButton($observer) {
        if ($observer->getBlock()->getType() != 'adminhtml/customer_edit') {
            return;
        }

        if (!Mage::helper('vs7_customeranonymize')->isEnabled()) {
            return;
        }

        $customer = Mage::registry('current_customer');
        if (!$customer->getId()) {
            return;
        }

        $location = Mage::helper('adminhtml')->getUrl(
            'adminhtml/vs7_customeranonymize/deletecustomer',
            array('id' => $customer->getId())
        );
        $data = array(
            'label' => 'Delete Customer and Anonymize Data',
            'onclick' => 'setLocation(\''.$location.'\')',
        );
        $observer->getBlock()->addButton('anonymize_button', $data);

//        $location = Mage::helper('adminhtml')->getUrl(
//            'adminhtml/vs7_customeranonymize/sendAnonymizeEmail',
//            array('id' => $customer->getId())
//        );
//        $data = array(
//            'label' => 'Send Anonymization Email',
//            'onclick' => 'setLocation(\''.$location.'\')',
//        );
//        $observer->getBlock()->addButton('anonymization_email_button', $data);
    }
}