<?php

class VS7_CustomerAnonymize_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_VS7_CUSTOMERANONYMIZE_ENABLED = 'vs7_customeranonymize/configuration/enabled';
    const XML_PATH_VS7_CUSTOMERANONYMIZE_INFORMATION_PAGE = 'vs7_customeranonymize/configuration/cms_page';
    const XML_PATH_VS7_CUSTOMERANONYMIZE_SUCCESSMESSAGE = 'vs7_customeranonymize/configuration/successmessage';

    public function isEnabled()
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_VS7_CUSTOMERANONYMIZE_ENABLED);
    }

    public function getSuccessMessage()
    {
        return Mage::getStoreConfig(self::XML_PATH_VS7_CUSTOMERANONYMIZE_SUCCESSMESSAGE);
    }

    public function getInformationPageIdentifier($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_VS7_CUSTOMERANONYMIZE_INFORMATION_PAGE, $store);
    }

    protected function anonymizeSaleAddress(&$address)
    {
        $address->setFirstname($this->getRandom());
        $address->setMiddlename($this->getRandom());
        $address->setLastname($this->getRandom());
        $address->setCompany($this->getRandom());
        $address->setEmail($this->getRandom('email'));
        $address->setRegion($this->getRandom());
        $address->setStreet($this->getRandom());
        $address->setCity($this->getRandom());
        $address->setPostcode($this->getRandom());
        $address->setTelephone($this->getRandom());
        $address->setFax($this->getRandom());
    }

    protected function anonymizeSaleData(&$obj)
    {
        $obj->setCustomerFirstname($this->getRandom());
        $obj->setCustomerMiddlename($this->getRandom());
        $obj->setCustomerLastname($this->getRandom());
        $obj->setCustomerEmail($this->getRandom('email'));
        $this->anonymizeSaleAddress($obj->getBillingAddress());
        $this->anonymizeSaleAddress($obj->getShippingAddress());
        $obj->save();
    }

    public function anonymizeCustomer($customer)
    {
        $orders = Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', $customer->getId());
        foreach ($orders as $order) {
            $this->anonymizeSaleData($order);
        }

        $quotes = Mage::getModel('sales/quote')->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', $customer->getId());
        foreach ($quotes as $quote) {
            $this->anonymizeSaleData($quote);
        }

        $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($customer->getEmail());
        if ($subscriber->getId()) {
            $subscriber->unsubscribe();
            $subscriber->delete();
        }

//        $email = Mage::getModel('core/email_template')->loadDefault('vs7_customeranonymize_success');
//        if ($email->getId()) {
//            $email->sendTransactional($email->getId(), 'sales', $customer->getEmail(), $customer->getName(), array(
//                'email' => $customer->getEmail(),
//                'name' => $customer->getName()
//            ), 0);
//        }

        $customer->delete();
    }

    public function getRandom($type = 'str')
    {
        $rand = substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', mt_rand(1, 10))), 1, 10);
        if ($type == 'str') {
            return $rand;
        } else if ($type == 'email') {
            return $rand . '@' . $rand . '.com';
        }
        return null;
    }
}