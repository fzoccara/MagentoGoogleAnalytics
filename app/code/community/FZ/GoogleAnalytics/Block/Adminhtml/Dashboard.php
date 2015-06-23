<?php

/**
 * @category   FZ
 * @package    FZ_GoogleAnalytics
 * @author     Francesco Zoccarato <francesco.zoccarato@gmail.com>
 */
class FZ_GoogleAnalytics_Block_Adminhtml_Dashboard extends Mage_Adminhtml_Block_Dashboard {

    protected function _prepareLayout() {
        parent::_prepareLayout();

        if (Mage::getStoreConfig(self::XML_PATH_ENABLE_CHARTS)) {
            $this->unsetChild('diagrams');
            $block = $this->getLayout()->createBlock('fz_googleanalytics/adminhtml_dashboard_diagrams');
            $this->setChild('diagrams', $block);
        }
    }

}
