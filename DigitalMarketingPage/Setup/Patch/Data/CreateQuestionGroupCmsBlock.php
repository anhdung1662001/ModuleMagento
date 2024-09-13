<?php

namespace Magenest\DigitalMarketingPage\Setup\Patch\Data;

use Magenest\DigitalMarketingPage\Api\CommonVar;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Api\GetBlockByIdentifierInterface;
use Magento\Store\Model\Store;

/**
 * class CreateQuestionGroupCmsBlock
 */
class CreateQuestionGroupCmsBlock implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param GetBlockByIdentifierInterface $getBlockByIdentifier
     * @param BlockFactory $blockFactory
     */
    public function __construct(
        protected ModuleDataSetupInterface      $moduleDataSetup,
        protected GetBlockByIdentifierInterface $getBlockByIdentifier,
        protected BlockFactory                  $blockFactory
    ) {}

    /**
     * @return void
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        try {
            $this->getBlockByIdentifier->execute(CommonVar::CMS_BLOCK_FAILURE_IDENTIFIER, Store::DEFAULT_STORE_ID);
        } catch (\Exception $e) {
            $this->blockFactory->create()
                ->setTitle('Question Group Failure Cms Block')
                ->setIdentifier(CommonVar::CMS_BLOCK_FAILURE_IDENTIFIER)
                ->setIsActive(true)
                ->setContent('<div>Question Group Failure Cms Block Content</div>')
                ->setStores([Store::DEFAULT_STORE_ID])
                ->save();
        }

        try {
            $this->getBlockByIdentifier->execute(CommonVar::CMS_BLOCK_SUCCESS_IDENTIFIER, Store::DEFAULT_STORE_ID);
        } catch (\Exception $e) {
            $this->blockFactory->create()
                ->setTitle('Question Group Success Cms Block')
                ->setIdentifier(CommonVar::CMS_BLOCK_SUCCESS_IDENTIFIER)
                ->setIsActive(true)
                ->setContent('<div>Question Group Success Cms Block Content</div>')
                ->setStores([Store::DEFAULT_STORE_ID])
                ->save();
        }

        $this->moduleDataSetup->endSetup();
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}
