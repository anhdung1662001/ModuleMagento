<?php

namespace Magenest\UTM\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magenest\UTM\Model\Session as UTMSession;

/**
 * class UTM
 */
class UTM extends \Magenest\DigitalMarketingPage\Helper\AbstractData
{
    /**
     * @var string[]
     */
    const SEARCH_QUERY_STRING = 'search_query_string';

    /**
     * @var string[]
     */
    const UTM_PARAM_STRING = 'utm_params_string';

    /**
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param Data $data
     * @param UTMSession $utmSession
     */
    public function __construct(
        Context                $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface  $storeManager,
        protected Data         $data,
        protected UTMSession   $utmSession
    )
    {
        parent::__construct(
            $context,
            $objectManager,
            $storeManager
        );
    }

    /**
     * @return mixed|string
     */
    public function getUtmParamsString()
    {
        $result = $this->utmSession->getData(self::UTM_PARAM_STRING);
        if ($searchQueryString = $this->utmSession->getData(self::SEARCH_QUERY_STRING)) {
            $result = $searchQueryString . '&' . $result;
        }
        return $result;
    }

    /**
     * @return void
     */
    public function handleUtmParams()
    {
        if (!$this->allowTrackingUTM()) {
            return;
        }
        try {
            if ($utmStringFromUri = $this->getUtmParamsFromUri()) {
                $this->utmSession->setData(self::UTM_PARAM_STRING, $utmStringFromUri);
            }

            if ($this->_request->getFullActionName() == 'catalogsearch_result_index') {
                $this->utmSession->setData(self::SEARCH_QUERY_STRING, $this->getQueryString());
            } else {
                $this->utmSession->setData(self::SEARCH_QUERY_STRING, null);
            }
        } catch (\Throwable $e) {
            $this->data->debug('Error while handle Utm Params', $e->getMessage());
        }
    }

    /**
     * @return mixed|string
     */
    protected function getQueryString()
    {
        $array = explode('?', $this->getRequestString());
        return count($array) == 2 ? last($array) : '';
    }

    /**
     * @return string
     */
    public function getUtmParamsFromUri()
    {
        $parsedUrl = parse_url($this->getRequestString(), PHP_URL_QUERY);
        $utmParams = [];
        if ($parsedUrl) {
            parse_str($parsedUrl, $queryParams);
            foreach ($queryParams as $key => $value) {
                if (strpos($key, 'utm_') === 0) {
                    $utmParams[$key] = $value;
                }
            }
        }
        return http_build_query($utmParams);
    }


    /**
     * @return mixed
     */
    public function getRequestString()
    {
        return $this->_request->getRequestString();
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function allowTrackingUTM()
    {
        return in_array($this->data->getCurrentWebsiteId(), $this->data->getWebsiteIds()) &&
            !$this->_request->isAjax() &&
            !$this->isEthicalPage();
    }

    /**
     * @return bool
     */
    private function isEthicalPage()
    {
        return $this->_request->getFullActionName() == 'cms_page_view' &&
            in_array(ltrim($this->getRequestString(), '/'), $this->data->getUrlKeyEthicalPage());
    }
}
