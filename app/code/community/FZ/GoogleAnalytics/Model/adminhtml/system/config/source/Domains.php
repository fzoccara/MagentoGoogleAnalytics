<?php

/**
 * @category   FZ
 * @package    FZ_GoogleAnalytics
 * @author     Francesco Zoccarato <francesco.zoccarato@gmail.com>
 */
class FZ_GoogleAnalytics_Model_Adminhtml_System_Config_Source_Domains {

    protected $_domains;

    public function toOptionArray($isMultiselect = true) {

        if (!$this->_domains) {
            $this->_domains = array();
            
            $domains = Mage::getModel('fz_googleanalytics/google_service_analytics')->getDomainsList();
            
            foreach ($domains as $domain) {
                $this->_domains[] = array(
                    'value' => $domain['id'],
                    'label' => $domain['name'] . ' (' . $domain['website_url'] . ')'
                );
            }
            
            if (!$isMultiselect) {
                array_unshift($this->_domains, array('value' => '', 'label' => Mage::helper('adminhtml')->__('--Please Select--')));
            }
        }

        return $this->_domains;
    }

}
