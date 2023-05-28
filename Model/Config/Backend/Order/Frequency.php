<?php
/**
 * @author QuangChien(Glorious Victory) <quangchien01.it@gmail.com>
 * @copyright Copyright © 2023 QuangChien(Glorious Victory) <https://www.facebook.com/quangchien01>. All rights reserved.
 */

namespace Victory\DeleteOrders\Model\Config\Backend\Order;

use Exception;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\App\Config\ValueFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Victory\DeleteOrders\Model\Config\Source\Frequency as ValueConfig;

class Frequency extends Value
{
    /**
     * Cron string path
     */
    const CRON_STRING_PATH = "crontab/default/jobs/delete_orders_cronjob/schedule/cron_expr";

    /**
     * Cron model path
     */
    const CRON_MODEL_PATH = "crontab/default/jobs/delete_orders_cronjob/run/model";

    /**
     * @var ValueFactory
     */
    protected $configValueFactory;

    /**
     * @var string
     */
    protected $runModelPath = '';

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param ValueFactory $configValueFactory
     * @param ManagerInterface $messageManager
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param $runModelPath
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        ValueFactory $configValueFactory,
        ManagerInterface $messageManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        $runModelPath = '',
        array $data = []
    ) {
        $this->runModelPath = $runModelPath;
        $this->configValueFactory = $configValueFactory;
        $this->messageManager = $messageManager;

        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @return Value
     */
    public function afterSave()
    {
        $time = $this->getData('groups/schedule/fields/start_time/value');
        $frequency = $this->getData('groups/schedule/fields/frequency/value');

        if ($frequency != (string)ValueConfig::DISABLE) {
            $cronExprArray = [
                (int) $time[1],
                (int) $time[0],
                $frequency === \Magento\Cron\Model\Config\Source\Frequency::CRON_MONTHLY ? '1' : '*',
                '*',
                $frequency === \Magento\Cron\Model\Config\Source\Frequency::CRON_WEEKLY ? '1' : '*'
            ];

            $cronExprString = join(' ', $cronExprArray);

            try {
                $this->configValueFactory->create()
                    ->load(self::CRON_STRING_PATH, 'path')
                    ->setValue($cronExprString)
                    ->setPath(self::CRON_STRING_PATH)
                    ->save();

                $this->configValueFactory->create()
                    ->load(self::CRON_MODEL_PATH, 'path')
                    ->setValue($this->runModelPath)
                    ->setPath(self::CRON_MODEL_PATH)
                    ->save();
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('We can\'t save the cron expression. %1', $e->getMessage()));
            }
        }else {
            try {
                $this->configValueFactory->create()
                    ->load(self::CRON_STRING_PATH, 'path')
                    ->setValue("0 0 30 2 *")
                    ->setPath(self::CRON_STRING_PATH)
                    ->save();

                $this->configValueFactory->create()
                    ->load(self::CRON_MODEL_PATH, 'path')
                    ->setValue($this->runModelPath)
                    ->setPath(self::CRON_MODEL_PATH)
                    ->save();
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('We can\'t save the cron expression. %1', $e->getMessage()));
            }
        }

        return parent::afterSave();
    }
}
