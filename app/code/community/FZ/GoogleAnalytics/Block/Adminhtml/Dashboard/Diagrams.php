<?php

/**
 * @category   FZ
 * @package    FZ_GoogleAnalytics
 * @author     Francesco Zoccarato <francesco.zoccarato@gmail.com>
 */
class FZ_GoogleAnalytics_Block_Adminhtml_Dashboard_Diagrams extends Mage_Adminhtml_Block_Dashboard_Diagrams {

    protected function _prepareLayout() {
        parent::_prepareLayout();
        if (Mage::getStoreConfig('fz_googleanalytics/dashboard/active')) {
            $this->addTab('realtime', array(
                'label' => $this->__('Realtime'),
                'content' => $this->getLayout()->createBlock('fz_googleanalytics/adminhtml_dashboard_tab_googleanalytics_realtime')->toHtml(),
                'active'    => Mage::getStoreConfig('fz_googleanalytics/dashboard/active_tab') == 'realtime'
            ));
            $this->addTab('trends', array(
                'label' => $this->__('Trends'),
                'content' => $this->getLayout()->createBlock('fz_googleanalytics/adminhtml_dashboard_tab_googleanalytics_trends')->toHtml(),
                'active'    => Mage::getStoreConfig('fz_googleanalytics/dashboard/active_tab') == 'trends'
            ));
        }
        return $this;
    }

}
