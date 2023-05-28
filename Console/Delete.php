<?php
/**
 * @author QuangChien(Glorious Victory) <quangchien01.it@gmail.com>
 * @copyright Copyright © 2023 QuangChien(Glorious Victory) <https://www.facebook.com/quangchien01>. All rights reserved.
 */

namespace Victory\DeleteOrders\Console;

use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Registry;
use Magento\Sales\Model\OrderRepository;
use Victory\DeleteOrders\Helper\Data;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Console\Cli;

class Delete extends Command
{
    const ORDER_ID = 'order_id';

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var state
     */
    protected $state;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param Data $helperData
     * @param OrderRepository $orderRepository
     * @param State $state
     * @param Registry $registry
     * @param $name
     */
    public function __construct(
        Data $helperData,
        OrderRepository $orderRepository,
        state $state,
        Registry $registry,
        $name = null
    ) {
        $this->helperData = $helperData;
        $this->orderRepository = $orderRepository;
        $this->state = $state;
        $this->registry = $registry;

        parent::__construct($name);
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('order:delete')
            ->setDescription('Delete order by id')
            ->addArgument(self::ORDER_ID, InputArgument::OPTIONAL, __('Order Id'));

        parent::configure();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->helperData->isEnabled()) {
            $output->writeln('<error>Please enable the module.</error>');
            return Cli::RETURN_FAILURE;
        }

        $this->state->setAreaCode(Area::AREA_ADMINHTML);
        $this->registry->unregister('isSecureArea');
        $this->registry->register('isSecureArea', true);

        $orderId = $input->getArgument(self::ORDER_ID);
        try {
            /** delete order*/
            $this->orderRepository->deleteById($orderId);
            /** delete order data on grid */
            $this->helperData->deleteOrderItem($orderId);

            $output->writeln('<info>The delete order process has been successful!</info>');
            return Cli::RETURN_SUCCESS;
        } catch (Exception $e) {
            $output->writeln("<error>The delete order process has been failure.</error>");
            $output->writeln("<error>{$e->getMessage()}</error>");
            return Cli::RETURN_FAILURE;
        }
    }
}
