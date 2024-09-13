<?php

namespace Magenest\UTM\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * class Data
 */
class Data extends \Magenest\DigitalMarketingPage\Helper\AbstractData
{
    /**
     * XML Path Ethical Page
     */
    const XML_PATH_ETHICAL_PAGE = 'magenest_utm/general/ethical_page';

    /**
     * XML Path website ids
     */
    const XML_PATH_WEBSITE_IDS = 'magenest_utm/general/website_ids';

    /**
     * @param LoggerInterface $logger
     * @param Json $json
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        protected LoggerInterface $logger,
        protected Json            $json,
        Context                   $context,
        ObjectManagerInterface    $objectManager,
        StoreManagerInterface     $storeManager
    )
    {
        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @return array
     */
    public function getUrlKeyEthicalPage()
    {
        try {
            return array_column($this->unserializeJson($this->_getEthicalPage(null)), 'url_key');
        } catch (\Throwable $e) {
            $this->debug('Error while get Url Key Ethical Page', $e->getMessage());
            return [];
        }
    }

    /**
     * @return string[]
     */
    public function getWebsiteIds()
    {
        return explode(',', $this->_getWebsiteIds(null) ?: '');
    }

    /**
     * @param $storeId
     * @return array|mixed
     */
    private function _getWebsiteIds($storeId)
    {
        return $this->getConfigValue(self::XML_PATH_WEBSITE_IDS, $storeId);
    }

    /**
     * @param $storeId
     * @return array|mixed
     */
    private function _getEthicalPage($storeId)
    {
        return $this->getConfigValue(self::XML_PATH_ETHICAL_PAGE, $storeId);
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCurrentWebsiteId()
    {
        return $this->storeManager->getWebsite()->getId();
    }

    /**
     * @param $json
     * @return array|bool|float|int|mixed|string|null
     */
    public function unserializeJson($json)
    {
        return $this->json->unserialize($json);
    }

    /**
     * @param $title
     * @param $message
     * @return void
     */
    public function debug($title, $message)
    {
        $this->logger->debug($title);
        $this->logger->error($message);
    }
}
