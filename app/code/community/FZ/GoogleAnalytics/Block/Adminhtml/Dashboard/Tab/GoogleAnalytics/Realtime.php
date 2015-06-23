<?php

/**
 * @category   FZ
 * @package    FZ_GoogleAnalytics
 * @author     Francesco Zoccarato <francesco.zoccarato@gmail.com>
 */
class FZ_GoogleAnalytics_Block_Adminhtml_Dashboard_Tab_GoogleAnalytics_Realtime extends FZ_GoogleAnalytics_Block_Adminhtml_Dashboard_Tab_GoogleAnalytics_Abstract {

    public function __construct() {
        $this->setHtmlId('realtime');
        parent::__construct();
        $this->setTemplate('fz/googleanalytics/dashboard/realtime.phtml');
    }
    
}
