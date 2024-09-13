<?php

namespace Magenest\UTM\Model\System\Config\Source;

use Magento\Store\Model\ResourceModel\Website\CollectionFactory;

/**
 * class Website
 */
class Website implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var
     */
    protected $_options;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        protected CollectionFactory $collectionFactory
    ) {}

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = $this->collectionFactory->create()->loadData()->toOptionArray();
        }
        return $this->_options;
    }
}
