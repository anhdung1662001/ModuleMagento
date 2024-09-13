<?php

namespace Magenest\DigitalMarketingPage\Ui\DataProvider\QuestionAnswers\Form\Modifier;

use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\DataType\Boolean;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\Component\Form\Element\ActionDelete;
use Magento\Ui\Component\DynamicRows as DynamicRowsCore;
use Magenest\DigitalMarketingPage\Model\ResourceModel\Question\CollectionFactory as questionCollectionFactory;
use Magenest\DigitalMarketingPage\Model\ResourceModel\Answer\CollectionFactory as AnswerCollectionFactory;

/**
 * class DynamicRows
 */
class DynamicRows implements ModifierInterface
{
    const FIELD_SET_GENERAL = 'general';
    const CONTAINER_HEADER_NAME = 'container_header';
    const GRID_ANSWERS_NAME = 'question_answers';
    const CONTAINER_QUESTION = 'container_question';
    const QUESTION_AND_ANSWERS = 'question_and_answers';
    const FIELD_IS_DELETE = 'is_delete';
    const FIELD_SORT_ORDER_NAME = 'sort_order';
    const FIELD_ANSWER = 'field_answer';
    const FIELD_TRUE_ANSWER = 'field_true_answer';


    /**
     * @param questionCollectionFactory $questionCollectionFactory
     * @param AnswerCollectionFactory $answerCollectionFactory
     */
    public function __construct(
        protected questionCollectionFactory $questionCollectionFactory,
        protected AnswerCollectionFactory   $answerCollectionFactory
    )
    {
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        $dataQuestion = $this->questionCollectionFactory->create()
            ->addFieldToFilter("question_group_id", $data['id']);
        $record = 0;
        foreach ($dataQuestion->getItems() as $item) {
            $data['question_answers'][] = [
                'record_id' => $record,
                'question' => $item->getQuestion(),
                'question_type' => $item->getQuestionType(),
                'answers' => $this->getAnswersByQuestionId($item->getId()),
                'question_id' => $item->getId()
            ];
            $record++;
        }
        return $data;
    }

    /**
     * @param $questionId
     * @return array
     */
    protected function getAnswersByQuestionId($questionId)
    {
        $dataAnswer = $this->answerCollectionFactory->create()
            ->addFieldToFilter("question_id", $questionId);
        $data = [];
        $record = 0;
        foreach ($dataAnswer->getItems() as $itemAnswer) {
            $data[] = [
                'record_id' => $record,
                'answer' => $itemAnswer->getAnswer(),
                'answer_id' => $itemAnswer->getId(),
                'true_answer' => $itemAnswer->getTrueAnswer()
            ];
            $record++;
        }
        return $data;
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        return $this->createCustomizeCalenderOptionsPanel($meta);
    }

    /**
     * @param $mate
     * @return array
     */
    public function createCustomizeCalenderOptionsPanel($mate)
    {
        $mate = array_replace_recursive(
            $mate, [
                static::FIELD_SET_GENERAL => [
                    'children' => [
                        static::CONTAINER_HEADER_NAME => $this->getButtonAddQuestion(10),
                        static::GRID_ANSWERS_NAME => $this->getDynamicQuestionAnswers(30)
                    ]
                ]
            ]
        );
        return $mate;
    }

    /**
     * @param $sortOrder
     * @return \array[][]
     */
    public function getButtonAddQuestion($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => null,
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'template' => 'ui/form/components/complex',
                        'sortOrder' => $sortOrder
                    ],
                ],
            ],
            'children' => [
                'add_new_question' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'title' => __('Add New Question'),
                                'formElement' => Container::NAME,
                                'componentType' => Container::NAME,
                                'component' => 'Magento_Ui/js/form/components/button',
                                'sortOrder' => 20,
                                'actions' => [
                                    [
                                        'targetName' => '${ $.ns }.${ $.ns }.' . static::FIELD_SET_GENERAL
                                            . '.' . static::GRID_ANSWERS_NAME,
                                        '__disableTmpl' => ['targetName' => false],
                                        'actionName' => 'processingAddChild',
                                    ]
                                ]
                            ]
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param $sortOrder
     * @return \array[][]
     */
    protected function getDynamicQuestionAnswers($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => DynamicRowsCore::NAME,
                        'component' => 'Magento_Catalog/js/components/dynamic-rows-import-custom-options',
                        'template' => 'ui/dynamic-rows/templates/collapsible',
                        'additionalClasses' => 'admin__field-wide',
                        'deleteProperty' => static::FIELD_IS_DELETE,
                        'deleteValue' => '1',
                        'addButton' => false,
                        'renderDefaultRecord' => false,
                        'columnsHeader' => false,
                        'collapsibleHeader' => true,
                        'sortOrder' => $sortOrder,
                        'dataProvider' => static::QUESTION_AND_ANSWERS,
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'headerLabel' => __('New Question And Answers'),
                                'componentType' => Container::NAME,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'isTemplate' => true,
                                'is_collection' => true,
                            ],
                        ],
                    ],
                    'children' => [
                        static::CONTAINER_QUESTION => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Fieldset::NAME,
                                        'collapsible' => true,
                                        'label' => null,
                                        'sortOrder' => 10,
                                        'opened' => true,
                                    ],
                                ],
                            ],
                            'children' => $this->getFields()
                        ],
                    ]
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    protected function getFields()
    {
        return [
            'question_type' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Question Type'),
                            'component' => 'Magenest_DigitalMarketingPage/js/form/question-answers/element/select-question-type',
                            'componentType' => Field::NAME,
                            'formElement' => Select::NAME,
                            'dataScope' => 'question_type',
                            'dataType' => Text::NAME,
                            'sortOrder' => 10,
                            'options' => [
                                ['value' => '0', 'label' => __('Choose 1 answer')]
//                                ['value' => '1', 'label' => __('Choose multiple answer')],
//                                ['value' => '2', 'label' => __('Fill in the answer')],
                            ],
                        ],
                    ],
                ],
            ],
            'question' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Question'),
                            'componentType' => Field::NAME,
                            'formElement' => Input::NAME,
                            'dataScope' => 'question',
                            'dataType' => Text::NAME,
                            'sortOrder' => 20,
                            'validation' => [
                                'required-entry' => true
                            ],
                        ],
                    ],
                ],
            ],
            'answers' => $this->addNewAnswers()
        ];
    }

    /**
     * @return \array[][]
     */
    protected function addNewAnswers()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'addButtonLabel' => __('Add Answer'),
                        'componentType' => DynamicRowsCore::NAME,
                        'component' => 'Magenest_DigitalMarketingPage/js/form/question-answers/dynamic-rows/answer',
                        'additionalClasses' => 'admin__field-wide',
                        'deleteProperty' => static::FIELD_IS_DELETE,
                        'deleteValue' => '1',
                        'renderDefaultRecord' => false,
                        'sortOrder' => 50,
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'positionProvider' => static::FIELD_SORT_ORDER_NAME,
                                'isTemplate' => true,
                                'is_collection' => true,
                            ],
                        ],
                    ],
                    'children' => [
                        static::FIELD_ANSWER => $this->getFieldAnswer(),
                        static::FIELD_TRUE_ANSWER => $this->getFieldTrueAnswer(),
                        static::FIELD_IS_DELETE => $this->getIsDeleteFieldConfig()

                    ]
                ]
            ]
        ];

    }

    /**
     * @return array[]
     */
    protected function getIsDeleteFieldConfig()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'dataType' => Text::NAME,
                        'componentType' => ActionDelete::NAME,
                        'fit' => false,
                        'template' => 'Magento_Backend/dynamic-rows/cells/action-delete',
                        'label' => __('Action'),
                        'sortOrder' => 3
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array[]
     */
    protected function getFieldAnswer()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Answer'),
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'dataScope' => 'answer',
                        'dataType' => Text::NAME,
                        'sortOrder' => 1,
                        'validation' => [
                            'required-entry' => true
                        ]
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array[]
     */
    protected function getFieldTrueAnswer()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('True Answer'),
                        'component' => 'Magenest_DigitalMarketingPage/js/form/question-answers/element/true-answer-toggle',
                        'componentType' => Checkbox::NAME,
                        'formElement' => Checkbox::NAME,
                        'dataScope' => 'true_answer',
                        'dataType' => Boolean::NAME,
                        'prefer' => 'toggle',
                        'sortOrder' => 2,
                        'valueMap' => [
                            'true' => '1',
                            'false' => '0'
                        ],
                        'default' => '0',
                        'validation' => [
                            'validate-true-answer' => true
                        ]
                    ],
                ],
            ]
        ];
    }

}
