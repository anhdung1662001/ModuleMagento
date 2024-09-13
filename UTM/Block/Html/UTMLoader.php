<?php

namespace Magenest\UTM\Block\Html;

use Magento\Framework\View\Element\Template;
use Magenest\UTM\Helper\UTM;

/**
 * class UTMLoader
 */
class UTMLoader extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = "Magenest_UTM::utm-loader.phtml";

    /**
     * @param UTM $helper
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        protected UTM    $helper,
        Template\Context $context,
        array            $data = []
    )
    {
        parent::__construct($context, $data);
        $this->helper->handleUtmParams();
    }

    /**
     * @return mixed|string
     */
    public function getUtmParamsString()
    {
        return $this->helper->getUtmParamsString();
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->helper->allowTrackingUTM();
    }
}
