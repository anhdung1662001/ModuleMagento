<?php

namespace Magenest\DigitalMarketingPage\Controller\Adminhtml\ResultSurvey;

use Magenest\DigitalMarketingPage\Model\TelephoneSurveyFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

/**
 * class Delete
 */
class Delete extends Action
{
    /**
     *
     */
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
        $resultPage = $this->resultRedirectFactory->create()->setPath('*/*/');

        if (!($id = $this->getRequest()->getParam('id'))) {
            $this->messageManager->addErrorMessage(__('ID is required'));
            return $resultPage;
        }

        try {
            $telephoneSurvey = $this->telephoneSurveyFactory->create()->load($id);
            if ($telephoneSurvey->getId()) {
                $telephoneSurvey->delete();
                $this->messageManager->addSuccessMessage(__('The result survey has been delete successfully!'));
                return $resultPage;
            }
            $this->messageManager->addErrorMessage(__('Result survey is not exist'));
        } catch (\Throwable $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $resultPage;
    }
}
