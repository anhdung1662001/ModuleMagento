<?php

namespace Magenest\DigitalMarketingPage\Model;

/**
 * class Question
 */
class Question extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\DigitalMarketingPage\Model\ResourceModel\Question');
    }
}
