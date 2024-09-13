<?php

namespace Magenest\DigitalMarketingPage\Block\Adminhtml\ResultSurvey\Form\Grid;

use Magenest\DigitalMarketingPage\Model\ResourceModel\ResultSurvey\CollectionFactory;
use Magenest\DigitalMarketingPage\Model\TelephoneSurveyFactory;
use Magento\Framework\View\Element\Template;

/**
 * class QuestionAndAnswers
 */
class QuestionAndAnswers extends \Magenest\DigitalMarketingPage\Block\Adminhtml\ResultSurvey\Form\View
{
    /**
     * @var string
     */
    protected $_template = "Magenest_DigitalMarketingPage::result_survey/form/grid/question-answers.phtml";

    /**
     * @var null
     */
    protected $_collection = null;

    /**
     * @param CollectionFactory $collectionFactory
     * @param TelephoneSurveyFactory $telephoneSurveyFactory
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        protected CollectionFactory $collectionFactory,
        TelephoneSurveyFactory      $telephoneSurveyFactory,
        Template\Context            $context,
        array                       $data = []
    )
    {
        parent::__construct($telephoneSurveyFactory, $context, $data);
    }

    /**
     * @return \Magenest\DigitalMarketingPage\Model\ResourceModel\ResultSurvey\Collection|null
     */
    public function getCollection()
    {
        if ($this->_collection == null) {
            $this->_collection = $this->collectionFactory->create()
                ->addFieldToFilter('telephone_survey_id', $this->getModelId())
                ->setPageSize(4)->setCurPage($this->geCurrentPage());
        }
        return $this->_collection;
    }

    /**
     * @param $data
     * @return string
     */
    public function getTotalTrueAnswer($data)
    {
        $total = array_column($data, 'curren_answer_is_true_answer');
        return count(array_filter($total)) . '/' . count($total);
    }

    /**
     * @return int
     */
    public function geTotalPage()
    {
        return $this->getCollection()->getLastPageNumber();
    }

    /**
     * @return array|int|mixed
     */
    public function geCurrentPage()
    {
        return $this->getData('current_page') ?? 1;
    }

    /**
     * @return bool
     */
    public function isShowPageToolbar()
    {
        $collection = $this->getCollection();
        return $collection && $collection->getSize() && $collection->getLastPageNumber() > 1;
    }
}
