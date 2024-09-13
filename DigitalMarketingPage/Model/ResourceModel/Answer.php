<?php

namespace Magenest\DigitalMarketingPage\Model\ResourceModel;

/**
 * class Answer
 */
class Answer extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magenest_answer', 'id');
    }

}
