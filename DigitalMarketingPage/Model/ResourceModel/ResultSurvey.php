<?php

namespace Magenest\DigitalMarketingPage\Model\ResourceModel;

/**
 * class ResultSurvey
 */
class ResultSurvey extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magenest_result_survey', 'id');
    }
}
