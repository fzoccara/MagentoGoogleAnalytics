<?php

class FZ_GoogleAnalytics_Block_Adminhtml_System_Config_Form_Field_Domain extends Mage_Adminhtml_Block_System_Config_Form_Field {

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {

        $id = Mage::getStoreConfig('fz_googleanalytics/dashboard/domain');
        if (!$id) {
            $element->setComment($this->__('Choose domain'));
        } else {
            $domain = Mage::getModel('fz_googleanalytics/google_service_analytics')->getDomainInfo($id);
            $infos = 'name: ' . $domain['name']
                    . '<br/>property_id: ' . $domain['property_id']
                    . '<br/>website_url: ' . $domain['website_url']
                    . '<br/>timezone: ' . $domain['timezone'];
            $element->setComment($infos);
        }

        return $element->getElementHtml();
    }

}
