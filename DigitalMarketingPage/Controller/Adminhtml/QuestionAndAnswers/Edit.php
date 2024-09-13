<?php

namespace Magenest\DigitalMarketingPage\Controller\Adminhtml\QuestionAndAnswers;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;

/**
 * class Edit
 */
class Edit extends Action
{
    const ADMIN_RESOURCE = 'Magenest_DigitalMarketingPage::question_answers';

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
