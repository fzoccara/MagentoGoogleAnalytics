<?php

class FZ_GoogleAnalytics_Block_Adminhtml_System_Config_Form_Field_Readonly extends Mage_Adminhtml_Block_System_Config_Form_Field {

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {

        $html = '<span id="' . $element->getHtmlId() . '">' . $element->getEscapedValue() . '</span>' . "\n";
        return $html;
    }

}
