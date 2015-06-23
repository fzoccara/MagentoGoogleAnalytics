<?php

/**
 * @category   FZ
 * @package    FZ_GoogleAnalytics
 * @author     Francesco Zoccarato <francesco.zoccarato@gmail.com>
 */
class FZ_GoogleAnalytics_Block_Adminhtml_Dashboard_Tab_GoogleAnalytics_Trends extends FZ_GoogleAnalytics_Block_Adminhtml_Dashboard_Tab_GoogleAnalytics_Abstract {

    protected $_defaultReport = 'montly';
    protected $_range = array();

    public function __construct() {
        $this->setHtmlId('trends');
        parent::__construct();

        $this->setRange();
        $this->setTemplate('fz/googleanalytics/dashboard/trends.phtml');
    }

    public function getReport() {
        $request = Mage::app()->getRequest();
        if ($request->getParam('report')) {
            return $request->getParam('report');
        }
        return $this->_defaultReport;
    }

    public function getRange($period = 'current') {
        if (!$this->_range || !count($this->_range)) {
            switch ($this->getReport()) {
                case 'yearly':
                    $fromCurrent = "-1 year";
                    $fromPrevious = "-2 year";
                    $toPrevious = "-1 year";
                    break;
                case 'weekly':
                    $fromCurrent = "-7 day";
                    $fromPrevious = "-14 day";
                    $toPrevious = "-7 day";
                    break;
                case 'montly':
                    $fromCurrent = "-1 month";
                    $fromPrevious = "-2 month";
                    $toPrevious = "-1 month";
                default:
                    break;
            }

            $this->_range['current'] = array();
            $this->_range['current']['from'] = Mage::getModel('core/date')->date('Y-m-d', strtotime($fromCurrent, time()));
            $this->_range['current']['to'] = Mage::getModel('core/date')->date('Y-m-d');
            $this->_range['previous'] = array();
            $this->_range['previous']['from'] = Mage::getModel('core/date')->date('Y-m-d', strtotime($fromPrevious, time()));
            $this->_range['previous']['to'] = Mage::getModel('core/date')->date('Y-m-d', strtotime($toPrevious, time()));
        }

        return (isset($this->_range[$period])) ? $this->_range[$period] : $this->_range;
    }

    public function getTrend($val1, $val2) {
        if ($val1 != 0) {
            $difference = $val2 - $val1;
            $fraction = $difference / $val1;

            return number_format($fraction * 100, 0);
        }
        return 0;
    }

}
