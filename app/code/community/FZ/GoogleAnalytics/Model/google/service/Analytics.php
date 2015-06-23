<?php

/**
 * @category   FZ
 * @package    FZ_GoogleAnalytics
 * @author     Francesco Zoccarato <francesco.zoccarato@gmail.com>
 */
class FZ_GoogleAnalytics_Model_Google_Service_Analytics extends FZ_GoogleAnalytics_Model_Google_Client {

    protected $_service;
    protected $_client;
    protected $_profileId;

    /*
     * 
     */

    public function __construct() {
        parent::__construct();

        try {
            $this->setService();

            // handle manage quota for realtime requests
            $baseUrlArr = str_replace(array('.', '-'), '_', parse_url(Mage::getBaseUrl()));

            if ($this->getProfileId()) {
                $this->_managequota = 's:' . $baseUrlArr['host'] . '-p' . $this->getProfileId();
                if (Mage::app()->getStore()->isAdmin()) {
                    $user = Mage::getSingleton('admin/session');
                    $userId = $user->getUser()->getUserId();
                    $this->_managequota .= '-u:' . $userId;
                }
            }
        } catch (apiServiceException $e) {
            Mage::getSingleton('core/session')->addError('There was an API error : ' . $e->getCode() . ' : ' . $e->getMessage());
            return false;
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError('There wan a general error : ' . $e->getMessage());
            return false;
        }

        return $this;
    }

    public function setService() {
        if (!$this->_service) {
            $this->_service = new Google_Service_Analytics($this->_client);
        }

        return $this;
    }

    public function getService() {
        return $this->_service;
    }

    public function getDomainInfo($id) {
        $profiles = $this->getDomainsList();
        if (isset($profiles[$id])) {
            return $profiles[$id];
        }
        return null;
    }

    public function getDomainsList() {

        if ($this->getClient()->getAccessToken()) {
            try {
                $profiles = $this->getService()->management_profiles->listManagementProfiles('~all', '~all');

                $items = $profiles->getItems();
                if (count($items) != 0) {
                    $profilesArray = array();
                    foreach ($items as $profile) {
                        $timetz = new DateTimeZone($profile->getTimezone());
                        $localtime = new DateTime('now', $timetz);
                        $timeshift = strtotime($localtime->format('Y-m-d H:i:s')) - time();
                        $profilesArray[$profile->getId()] = array(
                            'name' => $profile->getName(),
                            'id' => $profile->getId(),
                            'property_id' => $profile->getwebPropertyId(),
                            'website_url' => $profile->getwebsiteUrl(),
                            'timeshift' => $timeshift,
                            'timezone' => $profile->getTimezone()
                        );
                    }
                    return $profilesArray;
                }
            } catch (apiServiceException $e) {
                Mage::getSingleton('core/session')->addError('There was an API error : ' . $e->getCode() . ' : ' . $e->getMessage());
                return false;
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError('There wan a general error : ' . $e->getMessage());
                return false;
            }
        }


        return null;
    }

    public function getProfileId() {
        if (!$this->_profileId) {
            $this->_profileId = Mage::getStoreConfig('fz_googleanalytics/dashboard/domain');
        }
        return $this->_profileId;
    }

    /*
     * by default last month
     */

    public function getDatasByPeriods($from, $to, $metrics = array(), $options = array()) {
        if ($this->getProfileId()) {
            if (!$metrics) {
                $metrics = implode(',', $this->getTrendsMetrics());
            }
            try {
                $datas = $this->getService()->data_ga->get('ga:' . $this->getProfileId(), $from, $to, $metrics, $options);
                return $datas;
            } catch (apiServiceException $e) {
                Mage::getSingleton('core/session')->addError('There was an API error : ' . $e->getCode() . ' : ' . $e->getMessage());
                return false;
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError('There wan a general error : ' . $e->getMessage());
                return false;
            }
        }

        return null;
    }

    public function getTrendsMetrics() {
        return array('Hits' => 'ga:hits', 'Sessions' => 'ga:sessions', 'Bounce Rate (%)' => 'ga:bounceRate', 'Average Session Duration (seconds)' => 'ga:avgSessionDuration');
    }

    public function getTrendsMetricsFromKey($key) {
        $i = 0;
        foreach ($this->getTrendsMetrics() as $metric => $value) {
            if ($i++ == $key) {
                return $metric;
            }
        }
        return null;
    }
    public function getStructuredTrendsFromDatas($results) {
        $structured = array();

        if ($results && count($results->getRows()) > 0) {
            $rows = $results->getRows();

            foreach ($rows as $row) {
                foreach ($row as $key => $value) {
                    $dimensions = $this->getTrendsMetricsFromKey($key);
                    $structured[$dimensions] = $value;
                }
            }
        }

        return $structured;
    }
    
    public function getRealtimeDimensions() {
        return array('Device' => 'rt:deviceCategory', 'Visitor Type' => 'rt:visitorType', 'Traffic Type' => 'rt:trafficType', 'Visited Page Path' => 'rt:pagePath', 'Countries' => 'rt:country');
    }

    public function getRealtimeDimensionsFromKey($key) {
        $i = 0;
        foreach ($this->getRealtimeDimensions() as $dimension => $value) {
            if ($i++ == $key) {
                return $dimension;
            }
        }
        return null;
    }
    public function getStructuredRealtimeFromDatas($results) {
        $structured = array();

        if ($results && count($results->getRows()) > 0) {
            $rows = $results->getRows();

            foreach ($rows as $row) {
                foreach ($row as $key => $value) {
                    $dimensions = $this->getRealtimeDimensionsFromKey($key);
                    if (!isset($structured[$dimensions][$value])) {
                        $structured[$dimensions][$value] = 0;
                    }
                    $structured[$dimensions][$value] ++;
                }
            }
        }

        return $structured;
    }

    /*
     * by default last month
     */

    public function getDatasRealtime($metrics = 'rt:activeUsers', $options = array()) {
        if ($this->getProfileId()) {
            if (!$options['dimensions']) {
                $options['dimensions'] = implode(',', $this->getRealtimeDimensions());
            }
            $options['quotaUser'] = $this->_managequota;

            try {
                $datas = $this->getService()->data_realtime->get('ga:' . $this->getProfileId(), $metrics, $options);
                return $datas;
            } catch (apiServiceException $e) {
                Mage::getSingleton('core/session')->addError('There was an API error : ' . $e->getCode() . ' : ' . $e->getMessage());
                return false;
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError('There wan a general error : ' . $e->getMessage());
                return false;
            }
        }

        return null;
    }

    public function getRowsFromDatas($results) {
        $rows = array();

        if ($results && count($results->getRows()) > 0) {
            $rows = $results->getRows();
        }

        return $rows;
    }


    public function getCountFromDatas($results) {
        $total = 0;

        if ($results && $results->getRows()) {
            foreach ($results->getRows() as $row) {
                foreach ($row as $cell) {
                    $total += $cell;
                }
            }
        }

        return $total;
    }

//
//    public function runMainDemo() {
//        try {
//            $from = Mage::getModel('core/date')->date('Y-m-d', strtotime("-1 month", time()));
//            $to = Mage::getModel('core/date')->date('Y-m-d');
//            $results = $this->getDatasByPeriods($from, $to, 'ga:sessions');
//            echo 'this month: ' . $this->getCountFromDatas($results);
//            echo '<br/>';
//
//            $from = Mage::getModel('core/date')->date('Y-m-d', strtotime("-2 month", time()));
//            $to = Mage::getModel('core/date')->date('Y-m-d', strtotime("-1 month", time()));
//            $results = $this->getDatasByPeriods($from, $to, 'ga:sessions');
//            echo 'previous month: ' . $this->getCountFromDatas($results);
//            echo '<br/>';
//
//            $from = Mage::getModel('core/date')->date('Y-m-d', strtotime("-7 day", time()));
//            $to = Mage::getModel('core/date')->date('Y-m-d');
//            $results = $this->getDatasByPeriods($from, $to, 'ga:sessions');
//            echo 'this week (these 7 days): ' . $this->getCountFromDatas($results);
//            echo '<br/>';
//
//            $from = Mage::getModel('core/date')->date('Y-m-d', strtotime("-14 day", time()));
//            $to = Mage::getModel('core/date')->date('Y-m-d', strtotime("-7 day", time()));
//            $results = $this->getDatasByPeriods($from, $to, 'ga:sessions');
//            echo 'last week (lasts 7 days): ' . $this->getCountFromDatas($results);
//            echo '<br/>';
//
//            $resultsRealTime = $this->getDatasRealtime();
//            echo 'real time: ' . $this->getCountFromDatas($resultsRealTime);
//        } catch (apiServiceException $e) {
//            print 'There was an API error : ' . $e->getCode() . ' : ' . $e->getMessage();
//            return false;
//        } catch (Exception $e) {
//            print 'There wan a general error : ' . $e->getMessage();
//            return false;
//        }
//    }
//
//    public function getFirstprofileId() {
//        $accounts = $this->_service->management_accounts->listManagementAccounts();
//
//        if (count($accounts->getItems()) > 0) {
//            $items = $accounts->getItems();
//            $firstAccountId = $items[0]->getId();
//
//            $webproperties = $this->_service->management_webproperties
//                    ->listManagementWebproperties($firstAccountId);
//
//            if (count($webproperties->getItems()) > 0) {
//                $items = $webproperties->getItems();
//
//                $firstWebpropertyId = $items[0]->getId();
//
//                $profiles = $this->_service->management_profiles
//                        ->listManagementProfiles($firstAccountId, $firstWebpropertyId);
//
//                if (count($profiles->getItems()) > 0) {
//                    $items = $profiles->getItems();
//                    return $items[0]->getId();
//                } else {
//                    throw new Exception('No views (profiles) found for this user.');
//                }
//            } else {
//                throw new Exception('No webproperties found for this user.');
//            }
//        } else {
//            throw new Exception('No accounts found for this user.');
//        }
//    }
//    public function getResults($profileId) {
//        return $this->_service->data_ga->get(
//                        'ga:' . $profileId, '2012-03-03', '2015-03-03', 'ga:sessions');
//    }
//
//    if (!$client->getAccessToken()) {
//        $authUrl = $client->createAuthUrl();
//        print "<a class='login' href='$authUrl'>Connect Me!</a>";
//    } else {
//        // Create analytics service object. See next step below.
//
//        $analytics = new apiAnalyticsService($client);
//        try {
//
//            // Step 2. Get the user's first view (profile) ID.
//            $profileId = getFirstProfileId($analytics);
//
//            if (isset($profileId)) {
//
//                // Step 3. Query the Core Reporting API.
//                $results = getResults($analytics, $profileId);
//
//                // Step 4. Output the results.
//                printResults($results);
//            }
//        } catch (apiServiceException $e) {
//            // Error from the API.
//            print 'There was an API error : ' . $e->getCode() . ' : ' . $e->getMessage();
//        } catch (Exception $e) {
//            print 'There wan a general error : ' . $e->getMessage();
//        }
//    }
}
