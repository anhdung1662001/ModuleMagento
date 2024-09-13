<?php

namespace Magenest\DigitalMarketingPage\Controller\Adminhtml\ResultSurvey\View;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Json\Helper\Data as JsonData;

/**
 * class ListAjax
 */
class ListAjax extends Action
{
    /**
     * @param JsonData $jsonHelper
     * @param PageFactory $resultPageFactory
     * @param LoggerInterface $logger
     * @param Context $context
     */
    public function __construct(
        protected JsonData        $jsonHelper,
        protected PageFactory     $resultPageFactory,
        protected LoggerInterface $logger,
        Context                   $context
    )
    {
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $result = [];
        $request = $this->getRequest();
        if ($request->isPost() && ($currentPage = $request->getParam('current_page'))) {
            try {
                $block = $this->resultPageFactory->create()->getLayout()
                    ->createBlock('Magenest\DigitalMarketingPage\Block\Adminhtml\ResultSurvey\Form\Grid\QuestionAndAnswers',
                        'result_survey_html',
                        ['data' => ['current_page' => $currentPage]]
                    );
                $result['result_survey_html'] = $block->toHtml();
            } catch (\Throwable $e) {
                $this->logger->debug('Error while get reviews html');
                $this->logger->debug($e->getMessage());
            }
        }
        $this->getResponse()->representJson($this->jsonHelper->jsonEncode($result));
    }
}
