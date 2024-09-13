<?php

namespace Magenest\DigitalMarketingPage\Controller\Adminhtml\ResultSurvey;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;
use Magenest\DigitalMarketingPage\Model\TelephoneSurveyFactory;

/**
 * class View
 */
class View extends Action
{
    const ADMIN_RESOURCE = 'Magenest_DigitalMarketingPage::question_answers';

    /**
     * @param TelephoneSurveyFactory $telephoneSurveyFactory
     * @param Context $context
     */
    public function __construct(
        protected TelephoneSurveyFactory $telephoneSurveyFactory,
        Context                          $context
    )
    {
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if (!$id || !$this->telephoneSurveyFactory->create()->load($id)->getId()) {
            $this->messageManager->addErrorMessage(__('ID is invalid'));
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
