<?php

namespace Magenest\DigitalMarketingPage\Controller\Adminhtml\QuestionAndAnswers;

use Magenest\DigitalMarketingPage\Api\QuestionType;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magenest\DigitalMarketingPage\Model\QuestionGroupFactory;
use Magenest\DigitalMarketingPage\Model\QuestionFactory;
use Magenest\DigitalMarketingPage\Model\AnswerFactory;
use Magenest\DigitalMarketingPage\Model\ResourceModel\Question\CollectionFactory as QuestionCollectionFactory;
use Magenest\DigitalMarketingPage\Model\ResourceModel\Answer\CollectionFactory as AnswerCollectionFactory;

/**
 * class Save
 */
class Save extends Action
{
    const ADMIN_RESOURCE = 'Magenest_DigitalMarketingPage::question_answers';

    /**
     * @param AnswerCollectionFactory $answerCollectionFactory
     * @param QuestionCollectionFactory $questionCollectionFactory
     * @param QuestionGroupFactory $questionGroupFactory
     * @param AnswerFactory $answerFactory
     * @param QuestionFactory $questionFactory
     * @param Context $context
     */
    public function __construct(
        protected AnswerCollectionFactory   $answerCollectionFactory,
        protected QuestionCollectionFactory $questionCollectionFactory,
        protected QuestionGroupFactory      $questionGroupFactory,
        protected AnswerFactory             $answerFactory,
        protected QuestionFactory           $questionFactory,
        Context                             $context
    )
    {
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $data = $this->getRequest()->getParams();
            $questionGroupId = $data['id'] ?? null;

            //Save Question Group
            $dataQuestionGroup = [
                'question_group' => $data['question_group']
            ];
            $questionGroup = $this->questionGroupFactory->create();
            if ($questionGroupId) {
                $questionGroup->load($questionGroupId);
            }
            $questionGroup->addData($dataQuestionGroup)->save();

            //Save Question Of Question Group
            if (isset($data['question_answers'])) {
                $this->deleteQuestionNotInData($data['question_answers'], $questionGroupId);
                foreach ($data['question_answers'] as $questionInput) {
                    $question = $this->questionFactory->create();
                    $questionId = $questionInput['question_id'] ?? '';
                    if ($questionId) {
                        $question->load($questionId);
                    }
                    $dataQuestion = [
                        'question' => $questionInput['question'],
                        'question_type' => $questionInput['question_type'],
                        'question_group_id' => $questionGroup->getId()
                    ];
                    $question->addData($dataQuestion)->save();

                    if ($questionInput['question_type'] != QuestionType::FILL_IN_ANSWER) {
                        //Save Answer Of Question
                        if (isset($questionInput['answers'])) {
                            $this->deleteAnswerNotInData($questionInput['answers'], $questionId);
                            foreach ($questionInput['answers'] as $answerItem) {
                                $answer = $this->answerFactory->create();
                                $answersId = $answerItem['answer_id'] ?? '';
                                if ($answersId) {
                                    $answer->load($answersId);
                                }
                                $dataAnswer = [
                                    'answer' => $answerItem['answer'],
                                    'true_answer' => $answerItem['true_answer'] ?? null,
                                    'question_id' => $question->getId()
                                ];
                                $answer->addData($dataAnswer);
                                $answer->save();
                            }
                        } elseif ($questionId) {
                            $this->deleteAnswersByQuestionId($questionId);
                        }
                    } elseif ($questionId) {
                        $this->deleteAnswersByQuestionId($questionId);
                    }
                }
            } elseif ($questionGroupId) {
                $this->deleteQuestionByQuestionGroupId($questionGroupId);
            }
            $this->messageManager->addSuccessMessage(__('The question group has been saved successfully!'));
        } catch (\Throwable $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }

    /**
     * @param $data
     * @param $questionId
     * @return void
     */
    private function deleteAnswerNotInData($data, $questionId)
    {
        if ($data && $questionId) {
            $answerIdData = array_column($data, 'answer_id');
            $allAnswerId = $this->getAllAnswerIdByQuestionId($questionId);
            foreach (array_diff($allAnswerId, $answerIdData) as $id) {
                $this->answerFactory->create()->load($id)->delete();
            }
        }
    }

    /**
     * @param $data
     * @param $questionGroupId
     * @return void
     */
    private function deleteQuestionNotInData($data, $questionGroupId)
    {
        if ($data && $questionGroupId) {
            $questionIdData = array_column($data, 'question_id');
            $allQuestionId = $this->getAllQuestionIdByQuestionGroupId($questionGroupId);
            foreach (array_diff($allQuestionId, $questionIdData) as $id) {
                $this->questionFactory->create()->load($id)->delete();
            }
        }
    }

    /**
     * @param $questionGroupId
     * @return array
     */
    private function getAllQuestionIdByQuestionGroupId($questionGroupId)
    {
        return $this->questionCollectionFactory->create()
            ->addFieldToFilter('question_group_id', $questionGroupId)
            ->addFieldToSelect('id')
            ->getAllIds();
    }

    /**
     * @param $questionId
     * @return array
     */
    private function getAllAnswerIdByQuestionId($questionId)
    {
        return $this->answerCollectionFactory->create()
            ->addFieldToFilter('question_id', $questionId)
            ->addFieldToSelect('id')
            ->getAllIds();
    }

    /**
     * @param $questionId
     * @return void
     */
    private function deleteAnswersByQuestionId($questionId)
    {
        $answerCollection = $this->answerCollectionFactory->create()
            ->addFieldToFilter('question_id', $questionId)->getItems();
        foreach ($answerCollection as $item) {
            $item->delete();
        }
    }

    /**
     * @param $questionGroupId
     * @return void
     */
    private function deleteQuestionByQuestionGroupId($questionGroupId)
    {
        $questionCollection = $this->questionCollectionFactory->create()
            ->addFieldToFilter('question_group_id', $questionGroupId)->getItems();
        foreach ($questionCollection as $item) {
            $item->delete();
        }
    }
}
