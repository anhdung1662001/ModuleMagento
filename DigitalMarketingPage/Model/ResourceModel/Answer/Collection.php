<?php

namespace Magenest\DigitalMarketingPage\Model\ResourceModel\Answer;

/**
 * class Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\DigitalMarketingPage\Model\Answer', 'Magenest\DigitalMarketingPage\Model\ResourceModel\Answer');
    }
}
