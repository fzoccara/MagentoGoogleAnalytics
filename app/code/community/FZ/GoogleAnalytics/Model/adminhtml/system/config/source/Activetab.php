<?php

/**
 * @category   FZ
 * @package    FZ_GoogleAnalytics
 * @author     Francesco Zoccarato <francesco.zoccarato@gmail.com>
 */
class FZ_GoogleAnalytics_Model_Adminhtml_System_Config_Source_Activetab {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'default', 'label'=>Mage::helper('adminhtml')->__('Default')),
            array('value' => 'realtime', 'label'=>Mage::helper('adminhtml')->__('Realtime')),
            array('value' => 'trends', 'label'=>Mage::helper('adminhtml')->__('Trends'))
        );
    }

}
