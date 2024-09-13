<?php

namespace Magenest\DigitalMarketingPage\Model\ResourceModel;

/**
 * class TelephoneSurvey
 */
class TelephoneSurvey extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magenest_telephone_survey', 'id');
    }
}
