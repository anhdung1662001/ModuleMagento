<?php

namespace Magenest\ImportTelephone\Controller\ApplyDiscount;

use Magenest\ImportTelephone\Model\TelephoneDataImporter;
use Magento\Checkout\Helper\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Quote\Api\CartRepositoryInterface;

class Apply extends \Magento\Framework\App\Action\Action
{

    /**
     * @var JsonFactory
     */
    protected JsonFactory $resultJsonFactory;

    /**
     * @var Session
     */
    protected Session $_checkoutSession;

    /**
     * @var CartRepositoryInterface
     */
    protected CartRepositoryInterface $cartRepository;

    /**
     * @var TelephoneDataImporter
     */
    protected TelephoneDataImporter $importer;

    /**
     * Save constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Session $checkoutSession
     * @param CartRepositoryInterface $cartRepository
     * @param TelephoneDataImporter $importer
     */
    public function __construct(
        protected \Magenest\ExtendCheckout\Helper\Stock $heplerStocks,
        Context          $context,
        JsonFactory      $resultJsonFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        CartRepositoryInterface $cartRepository,
        TelephoneDataImporter $importer,
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_checkoutSession  = $checkoutSession;
        $this->cartRepository    = $cartRepository;
        $this->importer          = $importer;
    }


    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        try {
            $telephone = !empty($this->getRequest()->getParam('telephone')) ?
                $this->getRequest()->getParam('telephone') : '';
            $this->heplerStocks->showLogGetQuoteActive('app/code/Magenest/ImportTelephone/Controller/ApplyDiscount/Apply.php', $this->_checkoutSession->getQuote()->getId());

            $quote = $this->cartRepository->getActive($this->_checkoutSession->getQuote()->getId());
            $this->importer->import($quote, $telephone);
            $quote->setTotalsCollectedFlag(false);
            $quote->collectTotals();
            $this->cartRepository->save($quote);
            $response = [
                'success' => 'true'
            ];
        } catch (\Exception $e) {
            $response = [
                'success' => 'false'
            ];
            return $result->setData($response);
        }
        return $result->setData($response);
    }
}
