<?php

class FZ_GoogleAnalytics_Block_Adminhtml_System_Config_Form_Field_Obfuscated extends Mage_Adminhtml_Block_System_Config_Form_Field {

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $val = $element->getEscapedValue();
        $val = substr($val, 0, 12) . str_repeat("X", 30);
        $html = '<span id="' . $element->getHtmlId() . '">' . $val . '</span>' . "\n";
        return $html;
    }

}
