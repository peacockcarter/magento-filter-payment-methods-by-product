<?php

class PeacockCarter_FilterPaymentMethodsByProduct_Model_Attribute_Source_PaymentMethods extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * @return array
     */
    public function getAllOptions() //getActivePaymentMethods()
    {
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();

        $methods = array(array('value' => '', 'label' => Mage::helper('adminhtml')->__('--Please Select--')));

        foreach ($payments as $paymentCode => $paymentModel) {
            $paymentTitle          = Mage::getStoreConfig('payment/' . $paymentCode . '/title');
            $methods[$paymentCode] = array(
                'label' => $paymentTitle,
                'value' => $paymentCode,
            );
        }

        return $methods;
    }
}
