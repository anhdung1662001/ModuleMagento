<?php

namespace Magenest\DigitalMarketingPage\Controller\Adminhtml\QuestionAndAnswers;

use Magento\Backend\App\Action;
use Magenest\DigitalMarketingPage\Model\QuestionGroupFactory;
use Magento\Backend\App\Action\Context;

/**
 * class Delete
 */
class Delete extends Action
{
    const ADMIN_RESOURCE = 'Magenest_DigitalMarketingPage::question_answers';

    /**
     * @param QuestionGroupFactory $questionGroupFactory
     * @param Context $context
     */
    public function __construct(
        protected QuestionGroupFactory $questionGroupFactory,
        Context                        $context
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
            $questionGroup = $this->questionGroupFactory->create()->load($id);
            if ($questionGroup->getId()) {
                $questionGroup->delete();
                $this->messageManager->addSuccessMessage(__('The question group has been delete successfully!'));
                return $resultPage;
            }
            $this->messageManager->addErrorMessage(__('Question group is not exist'));
        } catch (\Throwable $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $resultPage;
    }
}
