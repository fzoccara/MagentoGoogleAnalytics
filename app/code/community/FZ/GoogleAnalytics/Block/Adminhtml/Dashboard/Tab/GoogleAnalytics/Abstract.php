<?php

/**
 * @category   FZ
 * @package    FZ_GoogleAnalytics
 * @author     Francesco Zoccarato <francesco.zoccarato@gmail.com>
 */
class FZ_GoogleAnalytics_Block_Adminhtml_Dashboard_Tab_GoogleAnalytics_Abstract extends Mage_Adminhtml_Block_Dashboard_Abstract {

    public function __construct() {
        parent::__construct();
    }
    
    public function getAnalytics(){
        return Mage::getModel('fz_googleanalytics/google_service_analytics');
    }

}
