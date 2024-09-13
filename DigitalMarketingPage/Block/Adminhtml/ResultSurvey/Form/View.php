<?php

namespace Magenest\DigitalMarketingPage\Block\Adminhtml\ResultSurvey\Form;

use Magenest\DigitalMarketingPage\Model\TelephoneSurveyFactory;
use Magento\Framework\View\Element\Template;

/**
 * class View
 */
class View extends Template
{
    /**
     * @var string
     */
    protected $_template = "Magenest_DigitalMarketingPage::result_survey/form/view.phtml";

    /**
     * @param TelephoneSurveyFactory $telephoneSurveyFactory
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        protected TelephoneSurveyFactory $telephoneSurveyFactory,
        Template\Context                 $context,
        array                            $data = []
    )
    {
        parent::__construct($context, $data);
    }

    /**
     * @return \Magenest\DigitalMarketingPage\Model\TelephoneSurvey
     */
    public function getTelephoneSurvey()
    {
        return $this->telephoneSurveyFactory->create()->load($this->getModelId());
    }

    /**
     * @return mixed
     */
    public function getModelId()
    {
        return $this->_request->getParam('id');
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['id' => $this->getModelId()]);
    }
}
