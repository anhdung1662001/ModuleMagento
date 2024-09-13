<?php

namespace Magenest\DigitalMarketingPage\Api\Data;

/**
 * interface QuestionGroupManagementInterface
 */
interface QuestionGroupManagementInterface
{
    /**
     * @param string $telephone
     * @return string
     */
    public function checkLimitNumberUsesTelephone(string $telephone);


    /**
     * @param string $data
     * @return mixed
     */
    public function complete($data);
}
