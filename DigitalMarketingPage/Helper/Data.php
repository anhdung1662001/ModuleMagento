<?php

namespace Magenest\DigitalMarketingPage\Helper;

use Magenest\DigitalMarketingPage\Api\XMLPath;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;

/**
 * class Data
 */
class Data extends AbstractData
{
    /**
     * @param Json $serializer
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        protected Json         $serializer,
        Context                $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface  $storeManager
    )
    {
        parent::__construct(
            $context,
            $objectManager,
            $storeManager
        );
    }

    /**
     * @param $storeId
     * @return array|mixed
     */
    public function getLimitNumberUsesTelephone($storeId)
    {
        return $this->getConfigValue(XMLPath::XML_PATH_LIMIT_NUMBER_USES_TELEPHONE, $storeId);
    }

    /**
     * @param $storeId
     * @return array|mixed
     */
    public function getCmsBlockSuccess($storeId)
    {
        return $this->getConfigValue(XMLPath::XML_PATH_CMS_BLOCK_SUCCESS, $storeId);
    }

    /**
     * @param $storeId
     * @return array|mixed
     */
    public function getCmsBlockFailure($storeId)
    {
        return $this->getConfigValue(XMLPath::XML_PATH_CMS_BLOCK_FAILURE, $storeId);
    }

    /**
     * @param $data
     * @return bool|string
     */
    public function serialize($data)
    {
        return $this->serializer->serialize($data);
    }

    /**
     * @param $data
     * @return array|bool|float|int|mixed|string|null
     */
    public function unserialize($data)
    {
        return $this->serializer->unserialize($data);
    }
}
