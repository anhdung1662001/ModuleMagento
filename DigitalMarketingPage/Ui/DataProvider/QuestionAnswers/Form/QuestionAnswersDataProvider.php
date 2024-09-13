<?php

namespace Magenest\DigitalMarketingPage\Ui\DataProvider\QuestionAnswers\Form;

use Magenest\DigitalMarketingPage\Model\ResourceModel\QuestionGroup\CollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

/**
 * class QuestionAnswersDataProvider
 */
class QuestionAnswersDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var array
     */
    protected $_loadedData;

    /**
     * @var \Magenest\DigitalMarketingPage\Model\ResourceModel\QuestionGroup\Collection
     */
    protected $collection;

    /**
     * @var PoolInterface|mixed
     */
    protected $modifiersPool;

    /**
     * @param PoolInterface|null $modifiersPool
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        PoolInterface     $modifiersPool = null,
        string            $name,
        string            $primaryFieldName,
        string            $requestFieldName,
        CollectionFactory $collectionFactory,
        array             $meta = [],
        array             $data = []
    )
    {

        $this->modifiersPool = $modifiersPool ?: ObjectManager::getInstance()->get(PoolInterface::class);
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (isset($this->_loadedData)) {
            return $this->_loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $item) {
            $this->_loadedData[$item['id']] = $this->getPreparedData($item->getData());
        }
        return $this->_loadedData;
    }

    /**
     * @inheritdoc
     * @since 103.0.0
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        /** @var ModifierInterface $modifier */
        foreach ($this->modifiersPool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }

    /**
     * @param $data
     * @return array|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getPreparedData($data)
    {
        /** @var ModifierInterface $modifier */
        foreach ($this->modifiersPool->getModifiersInstances() as $modifier) {
            $data = $modifier->modifyData($data);
        }

        return $data;
    }
}
