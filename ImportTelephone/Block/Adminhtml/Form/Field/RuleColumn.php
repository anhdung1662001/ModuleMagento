<?php

namespace Magenest\ImportTelephone\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as RuleCollection;
use Magento\Framework\View\Element\Context;

class RuleColumn extends Select
{
    /**
     * @var RuleCollection
     */
    protected $ruleColleciton;

    /**
     * @param RuleCollection $ruleColleciton
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        RuleCollection $ruleColleciton,
        Context $context,
        array $data = []
    ){
        $this->ruleColleciton = $ruleColleciton;
        parent::__construct($context, $data);
    }

    /**
     * Set "name" for <select> element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Set "id" for <select> element
     *
     * @param $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }
        return parent::_toHtml();
    }

    /**
     * @return array
     */
    private function getSourceOptions(): array
    {
        $ruleCollection = $this->ruleColleciton->create();
        $listRules = $ruleCollection->addFieldToFilter('is_active', 0)
            ->addFieldToFilter('discount_by_telephone', 1);
        $options = [];
        foreach ($listRules->getItems() as $rule){
            $options []= [
                'label' => $rule->getName(), 'value' => $rule->getRuleId(),
            ] ;
        }
        return $options;
    }
}