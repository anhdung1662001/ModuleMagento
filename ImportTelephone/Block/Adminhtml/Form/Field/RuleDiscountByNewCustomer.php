<?php

namespace Magenest\ImportTelephone\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magenest\ImportTelephone\Block\Adminhtml\Form\Field\RuleColumn;


class RuleDiscountByNewCustomer extends AbstractFieldArray
{

    /**
     * @var RuleColumn
     */
    private $ruleRenderer;


    /**
     * Prepare rendering the new field by adding all the needed columns
     */
    protected function _prepareToRender()
    {
        $this->addColumn('rule_discount', [
            'label' => __('Rule Discount For New Customer'),
            'renderer' => $this->getRuleRenderer()
        ]);
        $this->addColumn('rate_old_customer', [
            'label' => __('Discount Rate For Old Phone Number Group'),
             'class' => 'validate-number'
        ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }


    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];

        $rule = $row->getRule();
        if ($rule !== null) {
            $options['option_' . $this->getRuleRenderer()->calcOptionHash($rule)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }


    /**
     * @return RuleColumn
     * @throws LocalizedException
     */
    private function getRuleRenderer()
    {
        if (!$this->ruleRenderer) {
            $this->ruleRenderer = $this->getLayout()->createBlock(
                RuleColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->ruleRenderer;
    }

}
