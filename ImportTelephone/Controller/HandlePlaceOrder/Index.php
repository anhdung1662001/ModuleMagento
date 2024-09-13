<?php

namespace Magenest\ImportTelephone\Controller\HandlePlaceOrder;

use Magento\Framework\App\Action\Context;
use Magenest\ImportTelephone\Helper\Data;
use Magenest\Nemo\Helper\NemoHelper;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Quote\Api\CartRepositoryInterface;

class Index extends \Magento\Framework\App\Action\Action
{

    /**
     * @param Data $helper
     * @param NemoHelper $nemoHelper
     * @param JsonFactory $resultJsonFactory
     * @param CartRepositoryInterface $cartRepository
     * @param Context $context
     */
    public function __construct(
        protected Data                    $helper,
        protected NemoHelper              $nemoHelper,
        protected JsonFactory             $resultJsonFactory,
        protected CartRepositoryInterface $cartRepository,
        Context                           $context
    )
    {
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $response = [
            'success' => 'true'
        ];
        try {
            $quote = $this->cartRepository->getActive($this->nemoHelper->getCheckoutSession()->getQuote()->getId());
            $dataParams = $this->nemoHelper->getCheckoutSession()->getdataParams();
            $brandCode = !empty($dataParams['brand_code']) ? $dataParams['brand_code'] : "";
            $brandName = empty($brandCode) ? $this->nemoHelper->getBrandNameByStore() : $this->nemoHelper->convertBrandNameByBrandCode($brandCode);
            $oldNumber = $this->helper->isNewTelephone($quote->getBillingAddress()->getTelephone() , $brandName);
            if ($oldNumber) {
                $message = $this->helper->getPopupMessageOld();
            } else {
                $message = $this->helper->getPopupMessageNew();
            }
            foreach ($quote->getAllItems() as $item) {
                if (!empty($item->getStatusApplyRule()) && $item->getStatusApplyRule() == 'None') {
                    $response = [
                        'success' => 'false',
                        'message' => $message
                    ];
                }
            }
        } catch (\Exception $e) {
            $response = [
                'success' => 'false',
                'message' => __('There was an error when applying the voucher. Please contact the system for support.')
            ];
            return $result->setData($response);
        }
        return $result->setData($response);
    }
}
