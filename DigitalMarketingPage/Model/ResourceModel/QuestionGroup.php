<?php

namespace Magenest\DigitalMarketingPage\Model\ResourceModel;

/**
 * class QuestionGroup
 */
class QuestionGroup extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magenest_question_group', 'id');
    }
}
