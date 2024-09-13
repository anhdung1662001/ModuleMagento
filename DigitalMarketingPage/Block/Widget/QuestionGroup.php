<?php

namespace Magenest\DigitalMarketingPage\Block\Widget;

use Magenest\DigitalMarketingPage\Api\QuestionType;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magenest\DigitalMarketingPage\Model\QuestionGroupFactory;
use Magenest\DigitalMarketingPage\Model\ResourceModel\Question\CollectionFactory as QuestionCollectionFactory;
use Magenest\DigitalMarketingPage\Model\ResourceModel\Answer\CollectionFactory as AnswerCollectionFactory;
use Magenest\DigitalMarketingPage\Helper\Data;

/**
 * class QuestionGroup
 */
class QuestionGroup extends Template implements BlockInterface
{
    /**
     * @var string
     */
    protected $_template = "widget/question-group.phtml";
    /**
     * @var array
     */
    protected $_question_group_data = [];

    /**
     * @param Data $helper
     * @param AnswerCollectionFactory $answerCollectionFactory
     * @param QuestionCollectionFactory $questionCollectionFactory
     * @param QuestionGroupFactory $questionGroupFactory
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        protected Data                      $helper,
        protected AnswerCollectionFactory   $answerCollectionFactory,
        protected QuestionCollectionFactory $questionCollectionFactory,
        protected QuestionGroupFactory      $questionGroupFactory,
        Template\Context                    $context,
        array                               $data = []
    )
    {
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isShow()
    {
        return !empty($this->getQuestionGroupData()['questions']);
    }

    /**
     * @return array
     */
    public function getQuestionGroupData()
    {
        if ($this->_question_group_data === []) {
            $questionGroup = $this->questionGroupFactory->create()->load($this->getQuestionGroupId() ?: 0);
            if ($questionGroupId = $questionGroup->getId()) {
                $questionData = [];
                $questionCollection = $this->questionCollectionFactory->create()
                    ->addFieldToFilter('question_group_id', $questionGroupId);
                $questionCollection->getSelect()->orderRand();
                foreach ($questionCollection as $question) {
                    $answerData = [];
                    if ($question->getQuestionType() != QuestionType::FILL_IN_ANSWER) {
                        $answerCollection = $this->answerCollectionFactory->create()
                            ->addFieldToFilter('question_id', $question->getId())->getItems();
                        foreach ($answerCollection as $answer) {
                            $answerData[] = [
                                'answer_id' => $answer->getId(),
                                'answer' => $answer->getAnswer(),
                                'true_answer' => $answer->getTrueAnswer()
                            ];
                        }
                    }
                    $questionData[] = [
                        'question_id' => $question->getId(),
                        'question' => $question->getQuestion(),
                        'question_type' => $question->getQuestionType(),
                        'answers' => $answerData
                    ];
                }
                $questionGroupData = [
                    'title' => $this->getTitle(),
                    'question_group_id' => $questionGroupId,
                    'question_group' => $questionGroup->getQuestionGroup(),
                    'questions' => $questionData,
                    'number_true_answers_to_receive_reward' => $this->getConfigNumberTrueAnswersToReceiveReward(count($questionData)),
                    'limit' => $this->getLimit() && $this->validateNumber($this->getLimit()) ? $this->getLimit() : count($questionData)
                ];
                $this->_question_group_data = $questionGroupData;
            }
        }
        return $this->_question_group_data;
    }

    /**
     * @param $countQuestion
     * @return mixed
     */
    private function getConfigNumberTrueAnswersToReceiveReward($countQuestion)
    {
        $number = $this->getNumberTrueAnswersToReceiveReward();
        return $number && $this->validateNumber($number) && (int)$number <= $countQuestion ? $number : $countQuestion;
    }

    /**
     * @return bool|string
     */
    public function getDataJson()
    {
        $data = array_merge(
            $this->getQuestionGroupData(),
            [
                'content_popup_success' => $this->getBlockHtmlPopupSuccess(),
                'content_popup_failure' => $this->getBlockHtmlPopupFailure(),
            ]
        );
        return $this->helper->serialize($data);
    }

    /**
     * @param $value
     * @return bool
     */
    public function validateNumber($value)
    {
        return (bool)preg_match('/^[0-9]\d*$/', $value);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBlockHtmlPopupSuccess()
    {
        return $this->getLayout()
            ->createBlock('Magento\Cms\Block\Block')
            ->setBlockId($this->helper->getCmsBlockSuccess($this->helper->getStoreId()))
            ->toHtml();
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBlockHtmlPopupFailure()
    {
        return $this->getLayout()
            ->createBlock('Magento\Cms\Block\Block')
            ->setBlockId($this->helper->getCmsBlockFailure($this->helper->getStoreId()))
            ->toHtml();
    }
}
