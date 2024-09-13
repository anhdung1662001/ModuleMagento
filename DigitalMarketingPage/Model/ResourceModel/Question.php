<?php

namespace Magenest\DigitalMarketingPage\Model\ResourceModel;

/**
 * class Question
 */
class Question extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magenest_question', 'id');
    }

}
