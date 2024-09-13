<?php

namespace Magenest\ImportTelephone\Controller\Adminhtml\Telephone;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Magenest\ImportTelephone\Model\OldTelephoneFactory;
use Magenest\ImportTelephone\Model\ResourceModel\OldTelephone\CollectionFactory;

class Delete extends Action
{
    private $oldTelephoneNumberFactory;

    private $filter;
    private $collectionFactory;
    /**
     * @var RedirectFactory
     */
    private $resultRedirect;

    /**
     * @param Action\Context $context
     * @param OldTelephoneFactory $oldTelephoneNumberFactory
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param RedirectFactory $redirectFactory
     */
    public function __construct(
        Action\Context    $context,
        OldTelephoneFactory   $oldTelephoneNumberFactory,
        Filter            $filter,
        CollectionFactory $collectionFactory,
        RedirectFactory   $redirectFactory
    ){
        parent::__construct($context);
        $this->oldTelephoneNumberFactory = $oldTelephoneNumberFactory;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->resultRedirect = $redirectFactory;
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $total = 0;
        $err = 0;
        foreach ($collection->getItems() as $item) {
            $deletePost = $this->oldTelephoneNumberFactory->create()->load($item->getData('entity_id'));
            try {
                $deletePost->delete();
                $total++;
            } catch (LocalizedException $exception) {
                $err++;
            }
        }

        if ($total) {
            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been deleted.', $total)
            );
        }

        if ($err) {
            $this->messageManager->addErrorMessage(
                __(
                    'A total of %1 record(s) haven\'t been deleted. Please see server logs for more details.',
                    $err
                )
            );
        }
        return $this->resultRedirect->create()->setPath('telephone/telephone/oldtelephone');
    }
}
