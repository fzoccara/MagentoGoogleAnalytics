<?php

/**
 * @category   FZ
 * @package    FZ_GoogleAnalytics
 * @author     Francesco Zoccarato <francesco.zoccarato@gmail.com>
 */
class FZ_GoogleAnalytics_Model_Google_Client {

    const SCOPE = 'https://www.googleapis.com/auth/analytics.readonly';
    const ACCESS_TYPE = 'offline';
    const REDIRECT_URI = 'urn:ietf:wg:oauth:2.0:oob';

    protected $_service;
    protected $_client;
    protected $_options;

    public function __construct() {
        require_once(Mage::getBaseDir('lib') . '/Google/google-api-php-client/src/Google/Client.php');
        require_once(Mage::getBaseDir('lib') . '/Google/google-api-php-client/src/Google/Service/Analytics.php');
//        require_once(Mage::getBaseDir('lib') . '/Google/google-api-php-client/autoload.php');

        $this->setOptions();
        $this->setClient();

        return $this;
    }

    public function setOptions() {
        if (Mage::getStoreConfig('fz_googleanalytics/dashboard/use_own_app')) {
            $this->_options['api_key'] = Mage::getStoreConfig('fz_googleanalytics/dashboard/api_key');
            $this->_options['client_id'] = Mage::getStoreConfig('fz_googleanalytics/dashboard/client_id');
            $this->_options['client_secret'] = Mage::getStoreConfig('fz_googleanalytics/dashboard/client_secret');
            $this->_options['token'] = Mage::getStoreConfig('fz_googleanalytics/dashboard/token');
        } else {
            $this->_options['api_key'] = Mage::getStoreConfig('fz_googleanalytics/dashboard/default_api_key');
            $this->_options['client_id'] = Mage::getStoreConfig('fz_googleanalytics/dashboard/default_client_id');
            $this->_options['client_secret'] = Mage::getStoreConfig('fz_googleanalytics/dashboard/default_client_secret');
            $this->_options['token'] = Mage::getStoreConfig('fz_googleanalytics/dashboard/default_token');
        }
        $this->_options['secret_token'] = Mage::getStoreConfig('fz_googleanalytics/dashboard/secret_token');
    }

    public function getClient() {
        return $this->_client;
    }
    
    public function setClient() {
        if (!$this->_client) {
            $validCliendId = (isset($this->_options['client_id']) && $this->_options['client_id']);
            $validCliendSecret = (isset($this->_options['client_secret']) && $this->_options['client_secret']);
            $validSecretToken = (isset($this->_options['secret_token']) && $this->_options['secret_token']);
            $validToken = (isset($this->_options['token']) && $this->_options['token']);

            if ($validCliendId && $validCliendSecret) {

                try {

                    $this->_client = new Google_Client();
                    $this->_client->setApplicationName('Google Analytics Magento');

                    $this->_client->setClientId($this->_options['client_id']);
                    $this->_client->setClientSecret($this->_options['client_secret']);

                    $this->_client->setRedirectUri(self::REDIRECT_URI);
                    $this->_client->setAccessType(self::ACCESS_TYPE);
                    $this->_client->setScopes(array(self::SCOPE));

                    if ($this->_options['token']) {
                        if ($validSecretToken) {
                            $this->_client->setAccessToken($this->_options['secret_token']);
                        } else {
                            if ($validToken) {
                                $this->_client->authenticate($this->_options['token']);
                                $config = new Mage_Core_Model_Config();
                                $config->saveConfig('fz_googleanalytics/dashboard/secret_token', $this->_client->getAccessToken());
                            }
                        }
                    }
                    else{
                        $config = new Mage_Core_Model_Config();
                        $config->saveConfig('fz_googleanalytics/dashboard/secret_token', null);
                    }

                    return $this->_client;
                } catch (apiServiceException $e) {
                    Mage::getSingleton('core/session')->addError('There was an API error : ' . $e->getCode() . ' : ' . $e->getMessage());
                    return false;
                } catch (Exception $e) {
                    Mage::getSingleton('core/session')->addError('There wan a general error : ' . $e->getMessage());
                    return false;
                }
            }
        }
    }
}
