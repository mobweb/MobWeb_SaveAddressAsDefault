<?php

class MobWeb_SaveAddressAsDefault_Model_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
{
    protected function _prepareCustomerQuote()
    {
        $quote      = $this->getQuote();
        $billing    = $quote->getBillingAddress();
        $shipping   = $quote->isVirtual() ? null : $quote->getShippingAddress();

        $customer = $this->getCustomerSession()->getCustomer();
        if (!$billing->getCustomerId() || $billing->getSaveInAddressBook()) {
            $customerBilling = $billing->exportCustomerAddress();
            $customer->addAddress($customerBilling);
            $billing->setCustomerAddress($customerBilling);
        }
        if ($shipping && !$shipping->getSameAsBilling() &&
            (!$shipping->getCustomerId() || $shipping->getSaveInAddressBook())) {
            $customerShipping = $shipping->exportCustomerAddress();
            $customer->addAddress($customerShipping);
            $shipping->setCustomerAddress($customerShipping);
        }

        if (isset($customerBilling) && !$customer->getDefaultBilling()) {
            $customerBilling->setIsDefaultBilling(true);
        }
        if ($shipping && isset($customerShipping) && !$customer->getDefaultShipping()) {
            $customerShipping->setIsDefaultShipping(true);
        } else if (isset($customerBilling) && !$customer->getDefaultShipping()) {
            $customerBilling->setIsDefaultShipping(true);
        }

        // If the billing address is being saved in the address book, save it as default
        if(isset($customerBilling) && $billing->getSaveInAddressBook()) {
            $customerBilling->setIsDefaultBilling(true);
        }

        // If the shipping address is being saved in the address book, save it as default
        if(isset($customerShipping) && $shipping->getSaveInAddressBook()) {
            $customerShipping->setIsDefaultShipping(true);
        }

        $quote->setCustomer($customer);
    }
}