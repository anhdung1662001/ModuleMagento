<?php

namespace Magenest\UTM\Block\Adminhtml\System\Config\Form\Field;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * class UrlKeyEthicalPage
 */
class UrlKeyEthicalPage extends AbstractFieldArray
{
    /**
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn('url_key', ['label' => __('Url Key'), 'class' => 'no-marginal-whitespace']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }
}
