<?php

/**
 * @category   FZ
 * @package    FZ_GoogleAnalytics
 * @author     Francesco Zoccarato <francesco.zoccarato@gmail.com>
 */
class FZ_GoogleAnalytics_DashboardController extends Mage_Adminhtml_Controller_Action
{
    public function realtimeAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
    public function trendsAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
}
