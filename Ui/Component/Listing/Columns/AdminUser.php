<?php
/**
 * @author QuangChien(Glorious Victory) <quangchien01.it@gmail.com>
 * @copyright Copyright © 2023 QuangChien(Glorious Victory) <https://www.facebook.com/quangchien01>. All rights reserved.
 */

namespace Victory\DeleteOrders\Ui\Component\Listing\Columns;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Victory\DeleteOrders\Helper\Data;
use Magento\Backend\Model\Widget\Grid\Row\UrlGeneratorFactory;

class AdminUser extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var UrlGeneratorFactory
     */
    protected $urlGeneratorFactory;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param Data $helperData
     * @param UrlGeneratorFactory $urlGeneratorFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        Data $helperData,
        UrlGeneratorFactory $urlGeneratorFactory,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->helperData = $helperData;
        $this->urlGeneratorFactory = $urlGeneratorFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     * @throws LocalizedException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['entity_id'])) {
//                    $url = $this->urlGeneratorFactory->createUrlGenerator(\Magento\Backend\Model\UrlInterface::class, ['path' => 'admin/user/edit'])->getUrl($item);
//                    $item[$this->getData('name')] = "<a href='" . $url . "'>" . $this->helperData->getAdminUserName($item['admin_user']) . '</a>';
                    $item[$this->getData('name')] = $this->helperData->getAdminUserName($item['admin_user']);
                }
            }
        }
        return $dataSource;
    }
}
