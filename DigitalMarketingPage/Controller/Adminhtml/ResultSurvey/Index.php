<?php

namespace Magenest\DigitalMarketingPage\Controller\Adminhtml\ResultSurvey;

use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;

/**
 * class Index
 */
class Index extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Magenest_DigitalMarketingPage::result_survey';

    /**
     * @var PageFactory
     */
    protected $_pageFactory;

    /**
     * @param Action\Context $context
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Action\Context $context,
        PageFactory    $pageFactory
    )
    {
        $this->_pageFactory = $pageFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return $this->_pageFactory->create();
    }
}
