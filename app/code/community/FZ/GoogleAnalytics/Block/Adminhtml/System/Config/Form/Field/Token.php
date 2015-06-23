<?php

class FZ_GoogleAnalytics_Block_Adminhtml_System_Config_Form_Field_Token extends Mage_Adminhtml_Block_System_Config_Form_Field {

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {

        $client = Mage::getModel('fz_googleanalytics/google_client')->getClient();

        if ($client) {

            if ($client->getAccessToken()) {
                $element->setComment($this->__('Empty this field and save to reset authentication'));
            } else {
                try {
                    $authUrl = $client->createAuthUrl();

                    $element->setComment($this->__('Use this link to get the token: <a target="_blank" href="%s"=>link</a>.', $authUrl));
                } catch (Exception $e) {
                    $element->setComment($this->__('Error retrieving token link'));
                }
            }
        } else {
            $element->setComment($this->__('Please fill api key, client id and client secret fields and save to have the token link'));
        }
        return $element->getElementHtml();
    }

}
